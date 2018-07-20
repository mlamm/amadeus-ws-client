<?php
namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Flight\Service\Amadeus\Metrics\MetricsTracker;
use Flight\Service\Amadeus\Search\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Search\Exception\EmptyResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AmadeusClient
 * @package Flight\Service\Amadeus\Search\Model
 */
class AmadeusClient
{
    private const EMPTY_RESULT_ERRORS = [
        830, // No recommendation found with lower or equal price
        866, // No fare found for requested itinerary
        931, // No itinerary found for Requested Segment n
        977, // No available flight found for requested segment nn
        996, // NO JOURNEY FOUND FOR REQUESTED ITINERARY
    ];

    /**
     * Method invoked in amadeus api to trigger a search request.
     */
    public const SEARCH_ACTION = 'fareMasterPricerTravelBoardSearch';

    /**
     * @var ClientParamsFactory
     */
    protected $clientParamsFactory;

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
     * @var float The time when the request to amadeus starts.
     */
    protected $startTime;

    /**
     * @var MetricsTracker
     */
    private $metricsTracker;

    /**
     * @param ClientParamsFactory        $clientParamsFactory
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param AmadeusResponseTransformer $responseTransformer
     * @param \Closure                   $clientBuilder
     * @param MetricsTracker             $metricsTracker
     */
    public function __construct(
        ClientParamsFactory $clientParamsFactory,
        AmadeusRequestTransformer $requestTransformer,
        AmadeusResponseTransformer $responseTransformer,
        \Closure $clientBuilder,
        MetricsTracker $metricsTracker
    ) {
        $this->clientParamsFactory = $clientParamsFactory;
        $this->requestTransformer = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
        $this->clientBuilder = $clientBuilder;
        $this->metricsTracker = $metricsTracker;
    }

    /**
     * Method to start a search request based on a sent Request object
     *
     * @param Request      $request
     * @param BusinessCase $businessCase
     *
     * @return SearchResponse
     * @throws AmadeusRequestException
     * @throws EmptyResponseException
     * @throws \Exception
     */
    public function search(Request $request, BusinessCase $businessCase) : SearchResponse
    {
        /** @var Client $client */
        $clientParams = $this->clientParamsFactory->buildFromBusinessCase($businessCase);
        $client = ($this->clientBuilder)($clientParams);

        $requestOptions = $this->requestTransformer->buildFareMasterRequestOptions($request);

        $this->startTime = microtime(true);

        try {
            $result = $client->fareMasterPricerTravelBoardSearch($requestOptions);
            $this->trackLatency();
        } catch (\Exception $exception) {
            if ($this->isEmptyResponseError($exception)) {
                $this->trackLatency(Response::HTTP_BAD_REQUEST);
                throw new EmptyResponseException([$exception->getMessage()]);
            }

            $this->trackLatency(Response::HTTP_INTERNAL_SERVER_ERROR);
            throw $exception;
        }

        if ($this->isEmptyResultError($result)) {
            $this->trackLatency(Response::HTTP_BAD_REQUEST);
            throw new EmptyResponseException($result->messages);
        }

        if ($this->isErrorResponse($result)) {
            $this->trackLatency(Response::HTTP_INTERNAL_SERVER_ERROR);
            throw new AmadeusRequestException($result->messages);
        }

        return $this->responseTransformer->mapResultToDefinedStructure($businessCase, $request, $result);
    }

    /**
     * Does the response indicate an error?
     *
     * @param Client\Result $result
     * @return bool
     */
    private function isErrorResponse(Client\Result $result)
    {
        return $result->status !== Client\Result::STATUS_OK;
    }

    /**
     * Does the response error indicate that no flights were found?
     *
     * @param Client\Result $result
     * @return bool
     */
    private function isEmptyResultError(Client\Result $result)
    {
        return $this->isErrorResponse($result)
            && isset($result->messages[0])
            && in_array($result->messages[0]->code, self::EMPTY_RESULT_ERRORS);
    }

    /**
     * Does the error indicate that the response was empty?
     *
     * @param \Exception $exception
     * @return bool
     */
    private function isEmptyResponseError(\Exception $exception): bool
    {
        if ('Warning: DOMDocument::loadXML(): Empty string supplied as input' === $exception->getMessage()) {
            return true;
        }

        return false;
    }

    /**
     * @param int $statusCode
     *
     * @return void
     */
    private function trackLatency(int $statusCode = 200)
    {
        $this->metricsTracker->logResponseLatency(
            microtime(true) - $this->startTime,
            self::SEARCH_ACTION,
            $statusCode
        );
    }
}