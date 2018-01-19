<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Model\Remark;
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

        return $this->serializer->serialize($response, 'json');
    }

    public function remarksAdd($authHeader, $recordlocator, $body)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);
        $body = \GuzzleHttp\json_decode($body);

        $remarks = new ArrayCollection();
        foreach ($body as $remarkName => $remarkValue) {
            $remarks->add((new Remark())->setName($remarkName)->setValue($remarkValue));
        }

        $authenticate = (new Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});
        $response = $this->amadeusClient->remarksAdd(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksAdd())
                ->setRecordlocator($recordlocator)->setRemarks($remarks),
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }

    public function remarksDelete($authHeader, $recordlocator, $body)
    {
        // json data
        $authHeader = \GuzzleHttp\json_decode($authHeader);
        $body = \GuzzleHttp\json_decode($body);

        // authenticate
        $authenticate = (new Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        // get remarks for line number
        $response = $this->amadeusClient->remarksRead(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksRead())->setRecordlocator($recordlocator),
            $authenticate
        );

        // filter remarks tp delete
        /** @var ArrayCollection $remarksReadCollection */
        $remarksReadCollection = $response->getResult()->get(0);
        $remarksDeleteCollection = new ArrayCollection();
        /** @var Remark $remark */
        foreach ($remarksReadCollection as $remark) {
            foreach ($body as $remarkName => $remarkValue) {
                if ($remarkName == $remark->getName() && $remarkValue == $remark->getValue()) {
                    $remarksDeleteCollection->add($remark);
                }
            }
        }

        // be clean remove garbage
        unset($remarksReadCollection);

        $response = $this->amadeusClient->remarksDelete(
            (new \Flight\Service\Amadeus\Remarks\Request\Entity\RemarksDelete())
                ->setRecordlocator($recordlocator)->setRemarks($remarksDeleteCollection),
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }

    public function remarksModify($authHeader, $recordlocator, $body)
    {
        $this->remarksDelete($authHeader, $recordlocator, $body);

        return $this->remarksAdd($authHeader, $recordlocator, $body);
    }
}
