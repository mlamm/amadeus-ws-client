<?php

namespace Flight\Service\Amadeus\Session\Model;

use Flight\Service\Amadeus\Session\Request\Entity\Authenticate;
use \Flight\Service\Amadeus\Session\Exception\AmadeusRequestException;
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
     * result ok
     */
    public const CHECK_RESULT_OK = 1;

    /**
     * result already authenticate
     */
    public const CHECK_RESULT_ALREADY_AUTH = 2;

    /**
     * amadeus intern code for already authenticate
     */
    public const AMADEUS_RESULT_CODE_ALREADY_AUTH = '16001';

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
     * send create session request to amadeus
     *
     * @param Authenticate $authenticate
     * @return Session     * @throws \Exception
     * @throws Client\Exception
     * @throws AmadeusRequestException
     */
    public function createSession(Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        try {
            $clientResult = $client->securityAuthenticate();
        } catch (Client\Exception $e) {
            throw $e;
        }
        $checkResponseResult = $this->checkResult($clientResult);
        if (self::CHECK_RESULT_OK == $checkResponseResult) {
            $result = $this->responseTransformer->mapResultSessionCreate($client->getLastResponse());
        } else {
            $result = $this->responseTransformer->mapResultSessionCreateFromHeader($client->getLastResponse());
        }

        return $result;
    }

    /**
     * check result from AMA
     *
     * @param Client\Result $result
     * @return string check result (s. self::CHECK_RESULT_*)
     * @throws AmadeusRequestException
     */
    protected function checkResult(Client\Result $result): string
    {
        // result ok nothing to do
        if (Client\Result::STATUS_OK === $result->status) {
            return self::CHECK_RESULT_OK;
        }
        // already authenticate
        if (self::AMADEUS_RESULT_CODE_ALREADY_AUTH === $result->messages[0]->code) {
            return self::CHECK_RESULT_ALREADY_AUTH;
        }
        throw new AmadeusRequestException($result->messages);
    }
}