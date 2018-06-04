<?php

namespace Flight\Service\Amadeus\Session\Model;

use Flight\Service\Amadeus\Session\Request\Entity\Authenticate;
use Psr\Log\LoggerInterface;
use Amadeus\Client;

/**
 * AmadeusClient
 *
 * amadeus client for session request type
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusClient
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
     * @param Authenticate $authenticate
     * @return mixed
     * @throws \Exception
     */
    public function createSession(Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        try {
            $result = $client->securityAuthenticate();
        } catch (Client\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (Client\Result::STATUS_OK !== $result->status) {
            throw new \Exception($result->messages);
        }

        return $this->responseTransformer->mapResultSessionCreate($result);
    }
}