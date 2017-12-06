<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Service;

use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Flight\Service\Amadeus\Search\Cache\CacheKey;
use Flight\Service\Amadeus\Search\Cache\FlightCacheInterface;
use Flight\Service\Amadeus\Search\Model\AmadeusClient;
use Flight\Service\Amadeus\Search\Request\Validator\AmadeusRequestValidator;
use JMS\Serializer\Serializer;

/**
 * Search.php
 *
 * Service which searches the Amadeus Gds for flights.
 *
 * This class should not handle any aspects of the incoming http request (should stay in controller).
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Search
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
     * @var AmadeusClient
     */
    private $amadeusClient;

    /**
     * @var \stdClass
     */
    private $config;

    /**
     * @param AmadeusRequestValidator $requestValidator
     * @param Serializer              $serializer
     * @param FlightCacheInterface    $cache
     * @param AmadeusClient           $amadeusClient
     * @param \stdClass               $config
     */
    public function __construct(
        AmadeusRequestValidator $requestValidator,
        Serializer $serializer,
        FlightCacheInterface $cache,
        AmadeusClient $amadeusClient,
        \stdClass $config
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->amadeusClient = $amadeusClient;
        $this->config = $config;
    }

    /**
     * Perform the search.
     *
     * @param string $requestJson
     * @return string
     */
    public function search(string $requestJson) : string
    {
        // will throw on validation errors
        $this->requestValidator->validateRequest($requestJson);

        /** @var Request $searchRequest */
        $searchRequest = $this->serializer->deserialize($requestJson, Request::class, 'json');

        /** @var BusinessCase $businessCase */
        $businessCase = $searchRequest->getBusinessCases()->first()->first();

        $cacheKey = new CacheKey($searchRequest, $businessCase, $this->config);
        $cachedResponse = $this->cache->fetch((string) $cacheKey);

        if ($cachedResponse !== false) {
            return $cachedResponse;
        }

        $searchResponse = $this->amadeusClient->search($searchRequest, $businessCase);

        $serializedResponse =  $this->serializer->serialize($searchResponse, 'json');

        $this->cache->save((string) $cacheKey, $serializedResponse);

        return $serializedResponse;
    }
}
