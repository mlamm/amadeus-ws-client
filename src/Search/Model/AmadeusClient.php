<?php
namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Search\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Search\Exception\ServiceRequestAuthenticationFailedException;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Psr\Log\LoggerInterface;

/**
 * Class AmadeusClient
 * @package Flight\Service\Amadeus\Search\Model
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
     * The client must be created from request data. This makes it impossible to inject.
     * Therefore inject a factory method which builds the client.
     *
     * @var \Closure
     */
    protected $clientBuilder;

    /**
     * @param \stdClass                  $config
     * @param LoggerInterface            $logger
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param AmadeusResponseTransformer $responseTransformer
     * @param \Closure                   $clientBuilder
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
     *
     * @param Request $request
     * @param BusinessCase $businessCase
     *
     * @return SearchResponse
     * @throws AmadeusRequestException
     * @throws ServiceRequestAuthenticationFailedException
     */
    public function search(Request $request, BusinessCase $businessCase) : SearchResponse
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)($this->requestTransformer->buildClientParams($businessCase, $this->logger));

        $requestOptions = $this->requestTransformer->buildFareMasterRequestOptions($request);

        $result = $client->fareMasterPricerTravelBoardSearch($requestOptions);

        if ($result->status !== Client\Result::STATUS_OK) {
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultToDefinedStructure($businessCase, $result);
    }
}
