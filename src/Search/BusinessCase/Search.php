<?php
namespace Flight\Service\Amadeus\Search\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestException;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Search\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Search\Response\SearchResultResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Search
 * @package Flight\Service\Amadeus\Search\BusinessCase
 */
class Search extends BusinessCase
{
    /**
     * @var \Flight\Service\Amadeus\Search\Service\Search
     */
    protected $searchService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \Flight\Service\Amadeus\Search\Service\Search $searchService
     * @param LoggerInterface                       $logger
     */
    public function __construct(\Flight\Service\Amadeus\Search\Service\Search $searchService, LoggerInterface $logger)
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
