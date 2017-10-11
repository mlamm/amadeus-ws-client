<?php
namespace AmadeusService\Search\BusinessCase;

use Amadeus\Client\Result;
use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Exception\GeneralServerErrorException;
use AmadeusService\Application\Exception\ServiceException;
use AmadeusService\Application\Response\HalResponse;
use AmadeusService\Search\Exception\AmadeusRequestException;
use AmadeusService\Search\Exception\InvalidRequestException;
use AmadeusService\Search\Exception\InvalidRequestParameterException;
use AmadeusService\Search\Model\AmadeusResponseTransformer;
use AmadeusService\Search\Request\Validator\AmadeusRequestValidator;
use AmadeusService\Search\Response\AmadeusErrorResponse;
use AmadeusService\Search\Response\SearchResultResponse;
use AmadeusService\Search\Traits\SearchRequestMappingTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Search
 * @package AmadeusService\Search\BusinessCase
 */
class Search extends BusinessCase
{
    use SearchRequestMappingTrait;

    /**
     * @var AmadeusRequestValidator
     */
    protected $validator;

    /**
     * @var AmadeusResponseTransformer
     */
    protected $responseTransformer;

    /**
     * Search constructor.
     *
     * @param AmadeusResponseTransformer $responseTransformer
     * @param AmadeusRequestValidator    $validator
     */
    public function __construct(AmadeusResponseTransformer $responseTransformer, AmadeusRequestValidator $validator)
    {
        $this->responseTransformer = $responseTransformer;
        $this->validator = $validator;
    }

    /**
     * @return HalResponse
     */
    public function respond() : HalResponse
    {
        try {
            $this->validator->validateRequest($this->getRequest());
            // search process
            $request = $this->getMappedRequest($this->getRequest());

            $searchResult = $this->application['amadeus.client']
                ->search($request, $request->getBusinessCases()->first()->first());
            file_put_contents('var/logs/result_' . date('Y-m-d H:i:s') . '.xml', $searchResult->responseXml);

            if ($searchResult->status !== Result::STATUS_OK) {
                throw new AmadeusRequestException($searchResult->messages);
            }

            $mappedResponse = $this->responseTransformer->mapResultToDefinedStructure($request, $searchResult);

            return new SearchResultResponse(
                json_decode($this->responseTransformer->getMappedResponseAsJson($mappedResponse))
            );
        } catch (InvalidRequestException $ex) {
            $this->getLogger()->critical($ex);
            $ex->setResponseCode(Response::HTTP_BAD_REQUEST);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('search', $ex);
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $errorResponse;
        } catch (InvalidRequestParameterException $ex) {
            $this->getLogger()->debug($ex);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($ex->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $errorResponse->addMetaData([
                '_links' => [
                    'self' => ['href' => '/flight-search/']
                ]
            ]);

            return $errorResponse;
        } catch (ServiceException $ex) {

            // search exception handling
            $this->getLogger()->critical($ex);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('search', $ex);

            return $errorResponse;
        } catch (\Exception $ex) {

            // general exception handling
            $errorException = new GeneralServerErrorException($ex->getMessage());

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('_', $errorException);

            return $errorResponse;
        }
    }
}
