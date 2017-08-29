<?php
namespace AmadeusService\Search\BusinessCase;

use Amadeus\Client\Result;
use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Exception\GeneralServerErrorException;
use AmadeusService\Application\Exception\ServiceException;
use AmadeusService\Application\Response\ErrorResponse;
use AmadeusService\Search\Exception\AmadeusRequestException;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use AmadeusService\Search\Model\AmadeusClient;
use AmadeusService\Search\Model\AmadeusResponseTransformer;
use AmadeusService\Search\Response\SearchResultResponse;
use AmadeusService\Search\Traits\SearchRequestMappingTrait;
use Doctrine\DBAL\Connection;
use Flight\SearchRequestMapping\Entity\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Search
 * @package AmadeusService\Search\BusinessCase
 */
class Search extends BusinessCase
{
    use SearchRequestMappingTrait;

    /**
     * @return ErrorResponse|SearchResultResponse
     */
    public function respond()
    {
        try {
            // search process
            $request = $this->getMappedRequest($this->getRequest());

            /** @var Connection $cacheDatabase */
            $cacheDatabase = $this->get('database.ibe_cache');

            $amadeusClient = new AmadeusClient(
                $this->getLogger(),
                $request->getBusinessCases()->first()->first(),
                $cacheDatabase,
                getcwd() . '/wsdl/' . $this->getConfiguration()->search
            );

            $searchResult = $amadeusClient->search($request);

            if ($searchResult->status !== Result::STATUS_OK) {
                $ex = new AmadeusRequestException();
                $ex->assignError($searchResult->response->errorMessage);
                throw $ex;
            }

            $responseTransformer = new AmadeusResponseTransformer($searchResult);

            return new SearchResultResponse(
                json_decode($responseTransformer->getMappedResponseAsJson())
            );
        } catch (ServiceException $ex) {

            // search exception handling
            $this->getLogger()->critical($ex);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new ErrorResponse();
            $errorResponse->addViolation('search', $ex);

            return $errorResponse;
        } catch (\Exception $ex) {

            // general exception handling
            $errorException = new GeneralServerErrorException($ex->getMessage());

            $errorResponse = new ErrorResponse();
            $errorResponse->addViolation('_', $errorException);

            return $errorResponse;
        }
    }
}