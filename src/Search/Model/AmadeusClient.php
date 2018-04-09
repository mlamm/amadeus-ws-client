<?php
namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Flight\Service\Amadeus\Search\Exception\AmadeusRequestException;

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
     * @param ClientParamsFactory        $clientParamsFactory
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param AmadeusResponseTransformer $responseTransformer
     * @param \Closure                   $clientBuilder
     */
    public function __construct(
        ClientParamsFactory $clientParamsFactory,
        AmadeusRequestTransformer $requestTransformer,
        AmadeusResponseTransformer $responseTransformer,
        \Closure $clientBuilder
    ) {
        $this->clientParamsFactory = $clientParamsFactory;
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
     * @throws \Exception
     */
    public function search(Request $request, BusinessCase $businessCase) : SearchResponse
    {
        /** @var Client $client */
        $clientParams = $this->clientParamsFactory->buildFromBusinessCase($businessCase);
        $client = ($this->clientBuilder)($clientParams);

        $requestOptions = $this->requestTransformer->buildFareMasterRequestOptions($request);

        try {
            $result = $client->fareMasterPricerTravelBoardSearch($requestOptions);
        } catch (\Exception $exception) {
            if ($this->isEmptyResponseError($exception)) {
                return $this->responseTransformer->createEmptyResponse();
            }
            throw $exception;
        }

        if ($this->isEmptyResultError($result)) {
            // @TODO [ts] - MID - create a metric do track these behavior
            return $this->responseTransformer->createEmptyResponse();
        }

        if ($this->isErrorResponse($result)) {
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
}