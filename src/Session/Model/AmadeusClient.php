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
     * result ok
     */
    const CHECK_RESULT_OK = 1;

    /**
     * result already authenticate
     */
    const CHECK_RESULT_ALREADY_AUTH = 2;

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
            $clientResult = $client->securityAuthenticate();
        } catch (Client\Exception $e) {
            throw new \Exception($e->getMessage());
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
     * @throws \Exception
     */
    protected function checkResult(Client\Result $result): string
    {
        // result ok nothing to do
        if (Client\Result::STATUS_OK === $result->status) {
            return self::CHECK_RESULT_OK;
        }
        // already authenticate
        if ("16001" === $result->messages[0]->code) {
            return self::CHECK_RESULT_ALREADY_AUTH;
        }
        throw new \Exception(
            'something wrong by response from amadeus error code is ' . $result->messages[0]->code,
            'ARS0004'
        );
    }

    /**
     * @param Authenticate $authenticate
     * @return mixed
     * @throws \Exception
     */
    public function ignoreSession(Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $client->setSessionData(array(
            'sessionId' => '00I0B8DUM9',
            'sequenceNumber' => 1,
            'securityToken' => '2ZRRYNZ6P3GXM3JY0KUCMUVN7S'
        ));

        try {
            $result = $client->pnrIgnore(
                new Client\RequestOptions\PnrIgnoreOptions(array(
                    'actionRequest' => Client\Struct\Pnr\Ignore\ClearInformation::CODE_IGNORE
                ))
            );
        } catch (Client\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (Client\Result::STATUS_OK !== $result->status) {
            throw new \Exception($result->messages);
        }
        throw new \Exception(print_r($result, true));
        return $this->responseTransformer->mapSessionIgnore($result);
    }
}