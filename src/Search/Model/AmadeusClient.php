<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use Doctrine\DBAL\Connection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Leg;
use Flight\SearchRequestMapping\Entity\Request;
use Psr\Log\LoggerInterface;

/**
 * Class AmadeusClient
 * @package AmadeusService\Search\Model
 */
class AmadeusClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $tablePrefix = 'IbeFlightCache_';

    /**
     * AmadeusClient constructor.
     * @param LoggerInterface $logger
     * @param BusinessCase $businessCase
     * @param string $wsdlPath
     */
    public function __construct(
        LoggerInterface $logger,
        BusinessCase $businessCase,
        Connection $connection,
        $wsdlPath
    )
    {
        $this->connection = $connection;
        $this->client = new Client(
            new Client\Params(
                [
                    'authParams' => [
                        'officeId' => $businessCase->getAuthentication()->getOfficeId(),
                        'userId' => $businessCase->getAuthentication()->getUserId(),
                        'passwordData' => $businessCase->getAuthentication()->getPasswordData(),
                        'passwordLength' => $businessCase->getAuthentication()->getPasswordLength(),
                        'dutyCode' => $businessCase->getAuthentication()->getDutyCode(),
                        'organizationId' => $businessCase->getAuthentication()->getOrganizationId()
                    ],
                    'sessionHandlerParams' => [
                        'soapHeaderVersion' => Client::HEADER_V2,
                        'wsdl' => $wsdlPath,
                        'logger' => $logger
                    ],
                    'requestCreatorParams' => [
                        'receivedFrom' => 'service.search'
                    ]
                ]
            )
        );
    }

    /**
     * Method to start a search request based on a sent Request object
     * @param Request $request
     * @return Client\Result
     * @throws MissingRequestParameterException
     * @throws ServiceRequestAuthenticationFailedException
     */
    public function search(Request $request)
    {
        if ($this->checkFlightCache($request)) {
            return $this->retrieveFlightCache($request);
        }

        if ($request->getLegs()->count() < 1) {
            throw new MissingRequestParameterException();
        }

        $authResult = $this->client->securityAuthenticate();

        if ($authResult->status !== Client\Result::STATUS_OK) {
            throw new ServiceRequestAuthenticationFailedException();
        }

        $itineraries = [];

        /** @var Leg $leg */
        foreach ($request->getLegs() as $leg) {
            array_push(
                $itineraries,
                new Client\RequestOptions\Fare\MPItinerary(
                    [
                        'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                            [
                                'city' => $leg->getDeparture()
                            ]
                        ),
                        'arrivalLocation' => new Client\RequestOptions\Fare\MPLocation(
                            [
                                'city' => $leg->getArrival()
                            ]
                        ),
                        'date' => new Client\RequestOptions\Fare\MPDate(
                            [
                                'dateTime' => $leg->getDepartAt(),
                                // 'timeWindow' => 48 // +- h before/after
                            ]
                        )
                    ]
                )
            );
        }

        $options = new Client\RequestOptions\FareMasterPricerTbSearch(
            [
                'nrOfRequestedResults' => $request->getBusinessCases()->first()->first()->getResultLimit(),
                'nrOfRequestedPassengers' => $request->getPassengerCount(),
                'passengers' => $this->setupPassengers($request),
                'itinerary' => $itineraries,
                'flightOptions' => [
                    Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_ELECTRONIC_TICKET
                ],
                'airlineOptions' => [
                    // Client\RequestOptions\FareMasterPricerTbSearch::AIRLINEOPT_EXCLUDED => ['VY']
                ]
            ]
        );

        return $this->getClient()->fareMasterPricerTravelBoardSearch($options);
    }

    /**
     * Method to setup passengers to request for based on sent Request object
     * @param Request $request
     * @return Client\RequestOptions\Fare\MPPassenger[]
     */
    protected function setupPassengers(Request $request)
    {
        $passengers = [];

        if ($request->getAdults() > 0 ) {
            array_push(
                $passengers,
                new Client\RequestOptions\Fare\MPPassenger(
                    [
                        'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT,
                        'count' => $request->getAdults()
                    ]
                )
            );
        }

        if ($request->getChildren() > 0 ) {
            array_push(
                $passengers,
                new Client\RequestOptions\Fare\MPPassenger(
                    [
                        'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD,
                        'count' => $request->getChildren()
                    ]
                )
            );
        }

        if ($request->getInfants() > 0 ) {
            array_push(
                $passengers,
                new Client\RequestOptions\Fare\MPPassenger(
                    [
                        'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_INFANT,
                        'count' => $request->getInfants()
                    ]
                )
            );
        }

        return $passengers;
    }

    /**
     * Method to check if flight cache available for request
     *
     * @param Request $request
     * @return bool
     */
    public function checkFlightCache(Request $request)
    {
        $entropy = $this->createEntropy($request);
        $cacheKey = $this->createCacheKey($request);

        $result =
            $this->connection
                ->fetchAssoc("SELECT * FROM `{$this->tablePrefix}{substr($entropy, 0, 1)}` WHERE `CacheId` = ?", $cacheKey);

        return $result !== false;
    }

    /**
     * Method to retrieve a cached search result
     *
     * @param Request $request
     * @return Client\Result
     */
    public function retrieveFlightCache(Request $request)
    {
        $sendResult = new Client\Session\Handler\SendResult();
        $sendResult->responseXml = '';
        $sendResult->responseObject = '';
        return new Client\Result($sendResult);
    }

    /**
     * @param Request $request
     * @param null
     */
    public function putFlightCache(Request $request, $result)
    {
        $cacheKey = $this->createCacheKey($request);

    }

    /**
     * @param Request $request
     * @return string
     */
    protected function createEntropy(Request $request)
    {
        $entropy = '';
        return $entropy;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function createCacheKey(Request $request)
    {
        $values = [
            'nonStop' => $request->getBusinessCases()->first()->first()->getOptions()->get('is-non-stop'),
            'cabinClass' => reset($request->getFilterCabinClass()),
            'depAirline' => '',
            'paxAdt' => $request->getAdults(),
            'paxChd' => $request->getChildren(),
            'paxInf' => $request->getInfants(),
            'overnight' => '',
            'legs' => []
        ];

        /** @var Leg $leg */
        foreach($request->getLegs() as $leg) {
            $legValues = [
                'depAirport' => $leg->getDeparture(),
                'arrAirpot' => $leg->getArrival(),
                'date' => $leg->getDepartAt()->format('d.m.Y'),
                'datetime' => '',
                'timeRange' => '',
                'areaSearch' => []
            ];

            array_push($values['legs'], $legValues);
        }

        ksort($values);
        return md5(serialize($values));
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}