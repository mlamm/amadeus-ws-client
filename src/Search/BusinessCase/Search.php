<?php
namespace AmadeusService\Search\BusinessCase;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Exception\GeneralServerErrorException;
use AmadeusService\Application\Exception\ServiceException;
use AmadeusService\Application\Response\HalResponse;
use AmadeusService\Search\Exception\InvalidRequestException;
use AmadeusService\Search\Exception\InvalidRequestParameterException;
use AmadeusService\Search\Response\AmadeusErrorResponse;
use AmadeusService\Search\Response\SearchResultResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Search
 * @package AmadeusService\Search\BusinessCase
 */
class Search extends BusinessCase
{
    /**
     * @var \AmadeusService\Search\Service\Search
     */
    protected $searchService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \AmadeusService\Search\Service\Search $searchService
     * @param LoggerInterface                       $logger
     */
    public function __construct(\AmadeusService\Search\Service\Search $searchService, LoggerInterface $logger)
    {
        $this->searchService = $searchService;
        $this->logger = $logger;
    }

    /**
     * @return HalResponse
     */
    public function respond() : HalResponse
    {
        try {
            $response = SearchResultResponse::fromJsonString(
                $this->searchService->search($this->getRequest()->getContent())
            );
            $this->addLinkToSelf($response);
            return $response;
        } catch (InvalidRequestException $ex) {
            $this->logger->critical($ex);
            $ex->setResponseCode(Response::HTTP_BAD_REQUEST);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('search', $ex);
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (InvalidRequestParameterException $ex) {
            $this->logger->debug($ex);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($ex->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (ServiceException $ex) {

            // search exception handling
            $this->logger->critical($ex);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('search', $ex);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (\Throwable $ex) {

            // general exception handling
            $this->logger->critical($ex);
            $errorException = new GeneralServerErrorException($ex->getMessage());

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('_', $errorException);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        }
    }

    /**
     * Add the required self-link to the hal response
     *
     * @param HalResponse $response
     * @return HalResponse
     */
    private function addLinkToSelf(HalResponse $response) : HalResponse
    {
        return $response->addMetaData([
            '_links' => [
                'self' => ['href' => '/flight-search/']
            ]
        ]);
    }
}
