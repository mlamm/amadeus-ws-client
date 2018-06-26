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
     * @param $authHeader
     * @param $sessionHeader
     *
     * @return mixed|string
     *
     * @throws \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException
     */
    public function createPrice($authHeader, $sessionHeader, $body)
    {
        $authHeader    = \GuzzleHttp\json_decode($authHeader);
        $sessionHeader = \GuzzleHttp\json_decode($sessionHeader);
        $body       = \GuzzleHttp\json_decode($body);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateSession($sessionHeader);
        $this->requestValidator->validatePostBody($body);

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

        /**
         *         \Default_Service_Crs_PriceInterface::TARIFF_CODE_PUBLIC => array('FXP/LO'),
        \Default_Service_Crs_PriceInterface::TARIFF_CODE_PRIVATE => array('FXP/LO/R,U', 'FXP/LO/R,UP'),
        \Default_Service_Crs_PriceInterface::TARIFF_CODE_NETTO => array('FXP/LO/R,U000867'),
        \Default_Service_Crs_PriceInterface::TARIFF_CODE_CALCPUB => array('FXP/LO/R,U000867'),
         */

        $response = $this->amadeusClient->createPrice(
            $authenticate,
            $session,
            $body->tariff
        );

        return $this->serializer->serialize($response, 'json');
    }}
