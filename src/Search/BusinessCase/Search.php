<?php
namespace AmadeusService\Search\BusinessCase;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Exception\GeneralServerErrorException;
use AmadeusService\Application\Exception\ServiceException;
use AmadeusService\Application\Response\ErrorResponse;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use AmadeusService\Search\Model\AmadeusClient;
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
     * @return ErrorResponse|SearchResultResponse
     */
    public function respond()
    {
        try {
            $request = $this->getMappedRequest($this->getRequest());

            $amadeusClient = new AmadeusClient(
                $this->getLogger(),
                $request->getBusinessCase(),
                getcwd() . '/wsdl/' . $this->getConfiguration()->service->search->wsdl_name
            );

            return new SearchResultResponse($amadeusClient->search($request));
        } catch (ServiceException $ex) {
            $this->getLogger()->critical($ex);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new ErrorResponse();
            if ($ex instanceof ServiceRequestAuthenticationFailedException)
                $errorResponse->addViolation('search', $ex);

            return $errorResponse;
        } catch (\Exception $ex) {
            $errorException = new GeneralServerErrorException($ex->getMessage());

            $errorResponse = new ErrorResponse();
            $errorResponse->addViolation('_', $errorException);

            return $errorResponse;
        }
    }
}