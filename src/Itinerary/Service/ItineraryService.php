<?php

namespace Flight\Service\Amadeus\Itinerary\Service;

use Flight\Service\Amadeus\Itinerary\Request;
use JMS\Serializer\Serializer;
use Flight\Service\Amadeus\Itinerary\Model\ItineraryAmadeusClient;

/**
 * service for itinerary operations
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryService
{
    /**
     * @var Request\Validator\Itinerary
     */
    private $requestValidator;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ItineraryAmadeusClient
     */
    private $amadeusClient;

    /**
     * ItineraryService constructor.
     *
     * @param Request\Validator\Itinerary $requestValidator
     * @param Serializer                  $serializer
     * @param ItineraryAmadeusClient      $amadeusClient
     */
    public function __construct(
        Request\Validator\Itinerary $requestValidator,
        Serializer $serializer,
        ItineraryAmadeusClient $amadeusClient
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer       = $serializer;
        $this->amadeusClient    = $amadeusClient;
    }

    /**
     * @param $authHeader
     * @param $session
     * @param $recordLocator
     *
     * @return string
     * @throws \Amadeus\Client\Exception
     * @throws \Flight\Service\Amadeus\Itinerary\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Itinerary\Exception\InvalidRequestParameterException
     */
    public function read($authHeader, $session, $recordLocator) : string
    {
        $session    = \GuzzleHttp\json_decode($session);
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        // validate
        $this->requestValidator->validateSession($session);
        $this->requestValidator->validateAuthentication($authHeader);

        $authHeader = (new Request\Entity\Authenticate())->populate($authHeader);
        $session    = (new Request\Entity\Session())->populate($session);
        $itinEntity = new Request\Entity\ItineraryRead();
        if (isset($recordLocator)) {
            $itinEntity->setRecordLocator($recordLocator);
        }
        $response = $this->amadeusClient->itineraryRead(
            $itinEntity,
            $session,
            $authHeader
        );
        return $this->serializer->serialize($response->getResult()->toArray(), 'json');
    }
}
