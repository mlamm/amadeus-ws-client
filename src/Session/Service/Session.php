<?php

namespace Flight\Service\Amadeus\Session\Service;

use Flight\Service\Amadeus\Session\Model\AmadeusClient;
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
     * @var \stdClass
     */
    private $config;

    /**
     * @param Request\Validator\Session $requestValidator
     * @param Serializer                $serializer
     * @param AmadeusClient             $amadeusClient
     * @param \stdClass                 $config
     */
    public function __construct(
        Request\Validator\Session $requestValidator,
        Serializer $serializer,
        AmadeusClient $amadeusClient,
        \stdClass $config
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer       = $serializer;
        $this->amadeusClient    = $amadeusClient;
        $this->config           = $config;
    }

    /**
     * @param $authHeader
     * @return mixed|string
     * @throws \Exception
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

    public function ignoreSession($authHeader)
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

        $response = $this->amadeusClient->ignoreSession(
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }
}