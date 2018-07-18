<?php

namespace Flight\Service\Amadeus\Session\Model;

use Flight\Service\Amadeus\Session\Exception\InactiveSessionException;
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
     * interacted with an inactive session
     */
    const CHECK_RESULT_INACTIVE_CONVERSATION = 95;

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
     *
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
        } catch (Client\Exception $exception) {
            throw $exception;
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
     * send commit session request to amadeus
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     *
     * @return bool
     * @throws \Exception
     * @throws Client\Exception
     * @throws AmadeusRequestException
     */
    public function commitSession(Authenticate $authenticate, Session $session) : bool
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $pnrOptions = new Client\RequestOptions\PnrAddMultiElementsOptions(
            [
                'actionCode' => [
                    Client\RequestOptions\PnrAddMultiElementsOptions::ACTION_END_TRANSACT,
                    Client\RequestOptions\PnrAddMultiElementsOptions::ACTION_WARNING_AT_EOT,
                ],
            ]
        );

        try {
            $client->setSessionData($session->toArray());
            $clientResult = $client->pnrAddMultiElements($pnrOptions);
        } catch (Client\Exception $exception) {
            throw $exception;
        }
        $checkResponseResult = $this->checkResult($clientResult);

        if (self::CHECK_RESULT_OK == $checkResponseResult) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * send security sign out request to amadeus
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     *
     * @return bool
     * @throws \Exception
     * @throws Client\Exception
     * @throws AmadeusRequestException
     * @throws InactiveSessionException
     */
    public function closeSession(Authenticate $authenticate, Session $session) : bool
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        try {
            $client->setSessionData($session->toArray());
            $clientResult = $client->securitySignOut();
        } catch (Client\Exception $exception) {
            throw $exception;
        }
        $checkResponseResult = $this->checkResult($clientResult);
        if (self::CHECK_RESULT_OK == $checkResponseResult) {
            $result = true;
        } else {
            // inactive session check
            $this->checkResultSession($clientResult);
            $result = false;
        }

        return $result;
    }

    /**
     * check result from AMA
     *
     * @param Client\Result $result
     *
     * @return string check result (s. self::CHECK_RESULT_*)
     * @throws AmadeusRequestException
     */
    protected function checkResult(Client\Result $result) : string
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

    /**
     * @param Authenticate $authenticate
     * @param Session $session
     *
     * @return \Flight\Service\Amadeus\Session\Response\SessionCreateResponse
     *
     * @throws InactiveSessionException
     * @throws \Exception
     */
    public function ignoreSession(Authenticate $authenticate, Session $session)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $client->setSessionData(array(
            'sessionId' => $session->getSessionId(),
            'sequenceNumber' => $session->getSequenceNumber(),
            'securityToken' => $session->getSecurityToken()
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
            $this->checkResultSession($result);
        }

        return $this->responseTransformer->mapSessionIgnore($result);
    }

    /**
     * terminate given session
     *
     * @param Authenticate $authenticate
     * @param Session $session
     *
     * @return \Flight\Service\Amadeus\Session\Response\SessionCreateResponse
     *
     * @throws InactiveSessionException
     * @throws \Exception
     */
    public function terminateSession(Authenticate $authenticate, Session $session)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $client->setSessionData(array(
            'sessionId' => $session->getSessionId(),
            'sequenceNumber' => $session->getSequenceNumber(),
            'securityToken' => $session->getSecurityToken()
        ));

        try {
            $result = $client->securitySignOut();
        } catch (Client\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (Client\Result::STATUS_OK !== $result->status) {
            $this->checkResultSession($result);
        }

        return $this->responseTransformer->mapSessionTerminate($result);
    }

    /**
     * check session handling result for active session
     *
     * @param Client\Result $result result of session handling
     *
     * @throws InactiveSessionException
     * @throws \Exception
     */
    public function checkResultSession(Client\Result $result)
    {
        // result ok nothing to do
        if (AmadeusClient::CHECK_RESULT_INACTIVE_CONVERSATION == $result->messages[0]->code) {
            throw new InactiveSessionException(array('no active session'));
        }
        throw new \Exception(print_r($result->messages, true));
    }
}