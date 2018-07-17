<?php

namespace Flight\Service\Amadeus\Remarks\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Remarks\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Remarks\Request\Entity\Authenticate;
use Flight\Service\Amadeus\Remarks\Request\Entity\RemarksAdd;
use Flight\Service\Amadeus\Remarks\Request\Entity\RemarksDelete;
use Flight\Service\Amadeus\Remarks\Request\Entity\RemarksRead;
use Psr\Log\LoggerInterface;

/**
 * Class AmadeusClient
 *
 * @package Flight\Service\Amadeus\Remarks\Model
 */
class RemarksAmadeusClient
{
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
     * call for remarks read
     *
     * @param RemarksRead  $requestEntity
     * @param Authenticate $authenticate
     *
     * @return \Flight\Service\Amadeus\Remarks\Response\ResultResponse
     *
     * @throws AmadeusRequestException
     */
    public function remarksRead(RemarksRead $requestEntity, Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $requestOptions = $this->requestTransformer->buildOptionsRemarksRead($requestEntity->getRecordLocator());


        $result = $client->pnrRetrieve($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultRemarksRead($result);
    }

    /**
     * call for remarks add
     *
     * @param RemarksAdd   $requestEntity
     * @param Authenticate $authenticate
     *
     * @return \Flight\Service\Amadeus\Remarks\Response\ResultResponse
     *
     * @throws AmadeusRequestException
     */
    public function remarksAdd(RemarksAdd $requestEntity, Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $requestOptions = $this->requestTransformer->buildOptionsRemarksAdd(
            $requestEntity->getRecordLocator(),
            $requestEntity->getRemarks()
        );


        $result = $client->pnrAddMultiElements($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultRemarksAdd($result);
    }

    /**
     * call for remarks delete
     *
     * @param RemarksDelete $requestEntity
     * @param Authenticate  $authenticate
     *
     * @return \Flight\Service\Amadeus\Remarks\Response\ResultResponse
     *
     * @throws AmadeusRequestException
     */
    public function remarksDelete(RemarksDelete $requestEntity, Authenticate $authenticate)
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($authenticate, $this->logger));

        $requestOptions = $this->requestTransformer->buildOptionsRemarksDelete(
            $requestEntity->getRecordLocator(),
            $requestEntity->getRemarks()
        );

        $result = $client->pnrCancel($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultRemarksDelete($result);
    }
}
