<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Service;

use Flight\Service\Amadeus\Remarks\Request\Entity\Authenticate;
use Flight\Service\Amadeus\Remarks\Request\Validator\RemarksRead;
use Flight\Service\Amadeus\Remarks\Cache\CacheKey;
use Flight\Service\Amadeus\Search\Cache\FlightCacheInterface;
use Flight\Service\Amadeus\Remarks\Model\RemarksAmadeusClient;
use JMS\Serializer\Serializer;

/**
 * Remarks.php
 *
 * Service which remarkses the Amadeus Gds for flights.
 *
 * This class should not handle any aspects of the incoming http request (should stay in controller).
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Remarks
{
    /**
     * @var AmadeusRequestValidator
     */
    private $requestValidator;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FlightCacheInterface
     */
    private $cache;

    /**
     * @var RemarksAmadeusClient
     */
    private $amadeusClient;

    /**
     * @var \stdClass
     */
    private $config;

    /**
     * @param RemarksRead $requestValidator
     * @param Serializer              $serializer
     * @param FlightCacheInterface    $cache
     * @param RemarksAmadeusClient    $amadeusClient
     * @param \stdClass               $config
     */
    public function __construct(
        RemarksRead $requestValidator,
        Serializer $serializer,
        FlightCacheInterface $cache,
        RemarksAmadeusClient $amadeusClient,
        \stdClass $config
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->amadeusClient = $amadeusClient;
        $this->config = $config;
    }

    /**
     * Perform the remarks.
     *
     * @param string $requestJson
     * @return string
     */
    public function remarks(string $requestJson) : string
    {
        // will throw on validation errors
        $this->requestValidator->validateRequest($requestJson);

        /** @var Request $remarksRequest */
        $remarksRequest = $this->serializer->deserialize($requestJson, Request::class, 'json');

        /** @var BusinessCase $businessCase */
        $businessCase = $remarksRequest->getBusinessCases()->first()->first();

        $cacheKey = new CacheKey($remarksRequest, $businessCase, $this->config);
        $cachedResponse = $this->cache->fetch((string) $cacheKey);

        if ($cachedResponse !== false) {
            return $cachedResponse;
        }

        $remarksResponse = $this->amadeusClient->remarks($remarksRequest, $businessCase);

        $serializedResponse =  $this->serializer->serialize($remarksResponse, 'json');

        $this->cache->save((string) $cacheKey, $serializedResponse);

        return $serializedResponse;
    }

    public function remarksRead($authHeader, $recordlocator)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        $authenticate = (new Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});
        $response = $this->amadeusClient->remarksRead(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksRead())->setRecordlocator($recordlocator),
            $authenticate
        );
        return $response;
    }

    public function remarksAdd($authHeader, $recordlocator)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        $authenticate = (new Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});
        $response = $this->amadeusClient->remarksAdd(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksAdd())->setRecordlocator($recordlocator),
            $authenticate
        );
        return $response;
    }

    public function remarksDelete($authHeader, $recordlocator)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        $authenticate = (new Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});
        $response = $this->amadeusClient->remarksDelete(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksDelete())->setRecordlocator($recordlocator),
            $authenticate
        );
        return $response;
    }
}
