<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
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
     * AmadeusClient constructor.
     * @param LoggerInterface $logger
     * @param BusinessCase $businessCase
     * @param string $wsdlPath
     */
    public function __construct(
        LoggerInterface $logger,
        BusinessCase $businessCase,
        $wsdlPath
    )
    {
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

    public function search(Request $request)
    {
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
                                'dateTime' => $leg->getDepartAt()
                            ]
                        )
                    ]
                )
            );
        }

        $options = new Client\RequestOptions\FareMasterPricerTbSearch(
            [
                'nrOfRequestedResults' => $request->getBusinessCases()->first()->getResultLimit(),
                'nrOfRequestedPassengers' => $request->getPassengerCount(),
                'passengers' => $this->setupPassengers($request),
                'itinerary' => $itineraries
            ]
        );

        return $this->getClient()->fareMasterPricerTravelBoardSearch($options);
    }

    /**
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
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}