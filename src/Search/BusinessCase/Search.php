<?php

namespace Flight\Service\Amadeus\Search\BusinessCase;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Flight\Service\Amadeus\Application\Logger\ErrorLogger;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Search\Exception\EmptyResponseException;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestException;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Search\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Search\Response\SearchResultResponse;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Search
 *
 * @package Flight\Service\Amadeus\Search\BusinessCase
 */
class Search extends BusinessCase
{
    /**
     * @var \Flight\Service\Amadeus\Search\Service\Search
     */
    protected $searchService;

    /**
     * @var ErrorLogger
     */
    protected $errorLogger;

    /**
     * @param \Flight\Service\Amadeus\Search\Service\Search $searchService
     * @param ErrorLogger                                   $errorLogger
     */
    public function __construct(\Flight\Service\Amadeus\Search\Service\Search $searchService, ErrorLogger $errorLogger)
    {
        $this->searchService = $searchService;
        $this->errorLogger   = $errorLogger;
    }

    /**
     * @return HalResponse
     */
    public function respond(): HalResponse
    {
        try {
            $response = SearchResultResponse::fromJsonString(
                $this->searchService->search($this->getRequest()->getContent())
            );
        }
        catch (EmptyResponseException $ex) {
            $this->errorLogger->logException($ex, $this->getRequest(), Response::HTTP_BAD_REQUEST);
            $response = new SearchResultResponse((new SearchResponse)->setResult(new ArrayCollection));
        }
        catch (InvalidRequestException $ex) {
            $this->errorLogger->logException($ex, $this->getRequest(), Response::HTTP_BAD_REQUEST);
            $ex->setResponseCode(Response::HTTP_BAD_REQUEST);

            $response = new AmadeusErrorResponse();
            $response->addViolation('search', $ex);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } catch (InvalidRequestParameterException $ex) {
            $this->errorLogger->logException($ex, $this->getRequest(), Response::HTTP_BAD_REQUEST);

            $response = new AmadeusErrorResponse();
            $response->addViolationFromValidationFailures($ex->getFailures());
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } catch (ServiceException $ex) {
            $this->errorLogger->logException($ex, $this->getRequest(), Response::HTTP_BAD_REQUEST, LogLevel::ERROR);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $response = new AmadeusErrorResponse();
            $response->addViolation('search', $ex);
        } catch (\Throwable $ex) {
            $this->errorLogger->logException($ex, $this->getRequest(), Response::HTTP_BAD_REQUEST, LogLevel::CRITICAL);
            $errorException = new GeneralServerErrorException($ex->getMessage());

            $response = new AmadeusErrorResponse();
            $response->addViolation('_', $errorException);
        }

        return $this->addLinkToSelf($response);
    }

    /**
     * Add the required self-link to the hal response
     *
     * @param HalResponse $response
     *
     * @return HalResponse
     */
    private function addLinkToSelf(HalResponse $response): HalResponse
    {
        return $response->addMetaData([
            '_links' => [
                'self' => ['href' => '/flight-search/']
            ]
        ]);
    }
}
