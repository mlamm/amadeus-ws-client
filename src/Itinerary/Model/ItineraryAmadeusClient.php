<?php

namespace Flight\Service\Amadeus\Itinerary\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Itinerary\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Itinerary\Request\Entity\Authenticate;
use Flight\Service\Amadeus\Itinerary\Request\Entity\Session;
use Flight\Service\Amadeus\Itinerary\Request\Entity\ItineraryRead;
use Flight\Service\Amadeus\Itinerary\Response\ResultResponse;
use Psr\Log\LoggerInterface;

/**
 * Class AmadeusClient
 *
 * @package Flight\Service\Amadeus\Itinerary\Model
 */
class ItineraryAmadeusClient
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AmadeusRequestTransformer
     */
    protected $requestTransformer;

    /**
     * @var AmadeusResponseTransformer
     */
    protected $responseTransformer;

    /**
     * The client must be created from request data. This makes it impossible to inject.
     * Therefore inject a factory method which builds the client.
     *
     * @var \Closure
     */
    protected $clientBuilder;

    /**
     * @param LoggerInterface            $logger
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param AmadeusResponseTransformer $responseTransformer
     * @param \Closure                   $clientBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        AmadeusRequestTransformer $requestTransformer,
        AmadeusResponseTransformer $responseTransformer,
        \Closure $clientBuilder
    ) {
        $this->logger              = $logger;
        $this->requestTransformer  = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
        $this->clientBuilder       = $clientBuilder;
    }

    /**
     * call for remarks read
     *
     * @param ItineraryRead $requestEntity
     * @param Session       $session
     * @param Authenticate  $authenticate
     *
     * @return ResultResponse
     *
     * @throws AmadeusRequestException
     * @throws Client\Exception
     */
    public function itineraryRead(ItineraryRead $requestEntity, Session $session, Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $requestOptions = $this->requestTransformer->buildOptionsItineraryRead(
            $requestEntity->getRecordLocator()
        );
        $client->setSessionData($session->toArray());
        $client->securityAuthenticate()->responseXml;
        $result = $client->pnrRetrieve($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }
        return $this->responseTransformer->mapResult($result);
    }
}
