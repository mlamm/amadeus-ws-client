<?php

namespace Flight\Service\Amadeus\Session\Service;

use Flight\Service\Amadeus\Session\Exception\InactiveSessionException;
use Flight\Service\Amadeus\Session\Model\AmadeusClient;
use Flight\Service\Amadeus\Session\Model\Session as SessionModel;
use Flight\Service\Amadeus\Session\Request;
use JMS\Serializer\Serializer;

/**
 * Session service
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Session
{
    /**
     * @var Request\Validator\Session
     */
    private $requestValidator;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var AmadeusClient
     */
    private $amadeusClient;

    /**
     * @param Request\Validator\Session $requestValidator
     * @param Serializer                $serializer
     * @param AmadeusClient             $amadeusClient
     */
    public function __construct(
        Request\Validator\Session $requestValidator,
        Serializer $serializer,
        AmadeusClient $amadeusClient
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer       = $serializer;
        $this->amadeusClient    = $amadeusClient;
    }

    /**
     * @param $authHeader
     *
     * @return mixed|string
     * @throws \Amadeus\Client\Exception
     * @throws \Flight\Service\Amadeus\Session\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException
     */
    public function createSession($authHeader)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $response = $this->amadeusClient->createSession(
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * @param $authHeader
     * @param $sessionHeader
     *
     * @return mixed|string
     * @throws \Amadeus\Client\Exception
     * @throws \Flight\Service\Amadeus\Session\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException
     */
    public function commitSession($authHeader, $sessionHeader)
    {
        $authHeader    = \GuzzleHttp\json_decode($authHeader);
        $sessionHeader = \GuzzleHttp\json_decode($sessionHeader);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateSession($sessionHeader);

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $session = (new SessionModel())
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'})
            ->setSecurityToken($sessionHeader->{'security-token'});

        $response = $this->amadeusClient->commitSession(
            $authenticate,
            $session
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * @param $authHeader
     * @param $sessionHeader
     *
     * @return mixed|string
     * @throws \Amadeus\Client\Exception
     * @throws \Flight\Service\Amadeus\Session\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException
     * @throws InactiveSessionException
     */
    public function closeSession($authHeader, $sessionHeader)
    {
        $authHeader    = \GuzzleHttp\json_decode($authHeader);
        $sessionHeader = \GuzzleHttp\json_decode($sessionHeader);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateSession($sessionHeader);

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $session = (new SessionModel())
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'})
            ->setSecurityToken($sessionHeader->{'security-token'});

        $response = $this->amadeusClient->closeSession(
            $authenticate,
            $session
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * ignore given session
     *
     * @param $authHeader string json string with authentication information
     * @param $sessionHeader string json string with session information
     *
     * @return mixed|string
     *
     * @throws InactiveSessionException
     * @throws \Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException
     */
    public function ignoreSession($authHeader, $sessionHeader)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);
        $sessionHeader = \GuzzleHttp\json_decode($sessionHeader);


        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateSession($sessionHeader);

        $session = new \Flight\Service\Amadeus\Session\Model\Session();
        $session->setSecurityToken($sessionHeader->{'security-token'})
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'});

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $response = $this->amadeusClient->ignoreSession(
            $authenticate,
            $session
        );

        return $this->serializer->serialize($response, 'json');
    }
}
