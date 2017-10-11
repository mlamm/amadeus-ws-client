<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client;
use Amadeus\Client\Result;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use Doctrine\DBAL\Connection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
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
     * @var \stdClass
     */
    protected $config;

    /**
     * @var BusinessCaseAuthentication
     */
    protected $authentication;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AmadeusClient constructor.
     * @param LoggerInterface $logger
     * @param Connection $connection
     * @param $searchConfiguration
     */
    public function __construct(
        LoggerInterface $logger,
        Connection $connection,
        $searchConfiguration
    )
    {
        $this->config = $searchConfiguration;
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Method to start a search request based on a sent Request object
     * @param Request $request
     * @param BusinessCase $businessCase
     *
     * @return Result
     * @throws MissingRequestParameterException
     * @throws ServiceRequestAuthenticationFailedException
     */
    public function search(Request $request, BusinessCase $businessCase) : Result
    {
        $this->prepare($businessCase);

        // method to check if flight cache is available
        if ($this->checkFlightCache($request) && $this->config->cache_active) {
            return $this->retrieveFormattedFlightCache($request);
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
                'nrOfRequestedResults' => $request->getBusinessCases()->first()->first()->getOptions()->getResultLimit(),
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
     * builds the client
     *
     * @param BusinessCase $businessCase
     *
     * @return AmadeusClient
     */
    public function prepare(BusinessCase $businessCase) : AmadeusClient
    {
        $this->authentication = $businessCase->getAuthentication();
        $this->client = new Client(
            new Client\Params(
                [
                    'authParams' => [
                        'officeId' => $this->authentication->getOfficeId(),
                        'userId' => $this->authentication->getUserId(),
                        'passwordData' => $this->authentication->getPasswordData(),
                        'passwordLength' => $this->authentication->getPasswordLength(),
                        'dutyCode' => $this->authentication->getDutyCode(),
                        'organizationId' => $this->authentication->getOrganizationId()
                    ],
                    'sessionHandlerParams' => [
                        'soapHeaderVersion' => Client::HEADER_V2,
                        'wsdl' => "./wsdl/{$this->config->search->wsdl}",
                        'logger' => $this->logger
                    ],
                    'requestCreatorParams' => [
                        'receivedFrom' => 'service.search'
                    ]
                ]
            )
        );

        return $this;
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
     * Method to retrieve a cache entry by cache key
     *
     * @param string $cacheKey
     * @return array|bool
     */
    protected function queryCache(string $cacheKey)
    {
        $query = "SELECT * FROM `{$this->createTableName($cacheKey)}` WHERE `CacheId` = ?";
        return $this->connection->fetchAssoc($query, [$cacheKey]);
    }

    /**
     * Method to check if flight cache is available for request
     *
     * @param Request $request
     * @return bool
     */
    public function checkFlightCache(Request $request): bool
    {
        $cacheKey = $this->createCacheKey($request);
        return $this->queryCache($cacheKey) !== false;
    }

    /**
     * Method to retrieve a cached search result
     *
     * @param Request $request
     * @return Client\Result
     */
    public function retrieveFormattedFlightCache(Request $request)
    {
        // request
        $cacheKey = $this->createCacheKey($request);
        $databaseResult = $this->queryCache($cacheKey);
        $result = $this->deserializeContent($databaseResult['Content']);

        // morph xml to std class collection
        $simpleXmlRepresentation = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
        $jsonRepresentation = json_encode($simpleXmlRepresentation);
        $stdClassRepresentation = json_decode($jsonRepresentation);

        $sendResult = new Client\Session\Handler\SendResult();
        $sendResult->responseXml = $result;
        $sendResult->responseObject = $stdClassRepresentation;
        return new Client\Result($sendResult);
    }

    /**
     * @param Request $request
     * @param $result
     * @return int
     */
    public function putFlightCache(Request $request, $result)
    {
        $cacheKey = $this->createCacheKey($request);
        // @TODO: INSERT OR UPDATE
        return 1;
    }

    /**
     * @param string $cacheKey
     * @return string
     */
    protected function createTableName($cacheKey): string
    {
        $tablePostfix = strtoupper(substr($cacheKey, 1, 1));
        return "{$this->tablePrefix}$tablePostfix";
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function createCacheKey(Request $request): string
    {
        $values = [
            'nonStop' => '',//$request->getBusinessCases()->first()->first()->getOptions()->get('is-non-stop'),
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
        return md5("{md5(serialize($values))}{$this->createEntropy()}");
    }

    /**
     * Method to deserialize content from the flight cache
     * @param string $content
     * @return string
     */
    private function deserializeContent(string $content): string
    {
        // @TODO: deserialize content
        return $content;
    }

    /**
     * Method to serialize content before pushing it to database
     * @param string $content
     * @return string
     */
    private function serializeContent(string $content): string
    {
        // @TODO: serialize content
        return $content;
    }

    /**
     * Method to create the entropy that is used in cache key determination
     *
     * @return string
     */
    protected function createEntropy()
    {
        $sourceOffice = $this->authentication->getOfficeId();
        $excludedAirlines = md5(json_encode($this->config->excluded_airlines));
        $requestOptions = $this->config->request_options;

        return "{$sourceOffice}_{$excludedAirlines}{json_encode($requestOptions)}";
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
