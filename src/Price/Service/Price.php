<?php

namespace Flight\Service\Amadeus\Price\Service;

use Flight\Service\Amadeus\Price\Model\AmadeusClient;
use Flight\Service\Amadeus\Price\Model\Session;
use Flight\Service\Amadeus\Price\Request;
use JMS\Serializer\Serializer;

/**
 * Price service
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Price
{
    /**
     * @var Request\Validator\Price
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
     * @param Request\Validator\Price $requestValidator
     * @param Serializer              $serializer
     * @param AmadeusClient           $amadeusClient
     */
    public function __construct(
        Request\Validator\Price $requestValidator,
        Serializer $serializer,
        AmadeusClient $amadeusClient
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer       = $serializer;
        $this->amadeusClient    = $amadeusClient;
    }

    /**
     * @param $authHeader
     * @param $sessionHeader
     *
     * @return mixed|string
     *
     * @throws \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException
     */
    public function deletePrice($authHeader, $sessionHeader)
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

        $session = (new Session())
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'})
            ->setSecurityToken($sessionHeader->{'security-token'});

        $response = $this->amadeusClient->deletePrice(
            $authenticate,
            $session
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * Create Pricing quote in CRS.
     *
     * @param string $authHeader    authentication header
     * @param string $sessionHeader session header
     * @param string $plainBody     body content of request
     *
     * @return mixed|string
     * @throws \Amadeus\Client\Exception
     * @throws \Amadeus\Client\InvalidMessageException
     * @throws \Amadeus\Client\RequestCreator\MessageVersionUnsupportedException
     * @throws \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException
     */
    public function createPrice($authHeader, $sessionHeader, $plainBody)
    {
        $authHeader    = \json_decode($authHeader);
        $sessionHeader = \json_decode($sessionHeader);
        $jsonBody      = \json_decode($plainBody);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateSession($sessionHeader);
        $this->requestValidator->validatePostBody($jsonBody);

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $session = (new Session())
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'})
            ->setSecurityToken($sessionHeader->{'security-token'});

        if (!empty($jsonBody->{'fare-family'})) {
            $response = $this->amadeusClient->createAndSafePrice($authenticate, $session, $jsonBody->tariff, $jsonBody->{'fare-family'});
        } else {
            $response = $this->amadeusClient->createAndSafePrice($authenticate, $session, $jsonBody->tariff);
        }

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * get Pricing quote from CRS.
     *
     * @param string $authHeader    authentication header
     * @param string $sessionHeader session header
     *
     * @return mixed|string
     *
     * @throws \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException
     */
    public function getPrice($authHeader, $sessionHeader)
    {
        $authHeader    = \json_decode($authHeader);
        $sessionHeader = \json_decode($sessionHeader);

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

        $session = (new Session())
            ->setSessionId($sessionHeader->{'session-id'})
            ->setSequenceNumber($sessionHeader->{'sequence-number'})
            ->setSecurityToken($sessionHeader->{'security-token'});

        $response = $this->amadeusClient->getPrice($authenticate, $session);
        return $this->serializer->serialize($response->getResult(), 'json');
    }
}
