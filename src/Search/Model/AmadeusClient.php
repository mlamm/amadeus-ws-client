<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client;
use Amadeus\Client\Result;
use AmadeusService\Search\Exception\AmadeusRequestException;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Psr\Log\LoggerInterface;

/**
 * Class AmadeusClient
 * @package AmadeusService\Search\Model
 */
class AmadeusClient
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
     * @var \Closure
     */
    protected $clientBuilder;

    /**
     * @param \stdClass                  $config
     * @param LoggerInterface            $logger
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param AmadeusResponseTransformer $responseTransformer
     */
    public function __construct(
        \stdClass $config,
        LoggerInterface $logger,
        AmadeusRequestTransformer $requestTransformer,
        AmadeusResponseTransformer $responseTransformer,
        \Closure $clientBuilder
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->requestTransformer = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
        $this->clientBuilder = $clientBuilder;
    }

    /**
     * Method to start a search request based on a sent Request object
     * @param Request $request
     * @param BusinessCase $businessCase
     *
     * @return Result
     * @throws MissingRequestParameterException
     * @throws ServiceRequestAuthenticationFailedException
     */
    public function search(Request $request, BusinessCase $businessCase) : SearchResponse
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($businessCase, $this->logger));

        $authResult = $client->securityAuthenticate();

        if ($authResult->status !== Client\Result::STATUS_OK) {
            throw new ServiceRequestAuthenticationFailedException($authResult->messages);
        }

        $requestOptions = $this->requestTransformer->buildFareMasterRequestOptions($request);

        $result = $client->fareMasterPricerTravelBoardSearch($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultToDefinedStructure($businessCase, $result);
    }
}
