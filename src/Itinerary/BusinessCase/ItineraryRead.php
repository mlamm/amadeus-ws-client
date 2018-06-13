<?php

namespace Flight\Service\Amadeus\Itinerary\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Itinerary\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Itinerary\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Itinerary\Response\ResultResponse;
use Flight\Service\Amadeus\Itinerary\Service\ItineraryService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItineraryRead BusinessCase
 *
 * @package Flight\Service\Amadeus\Itinerary\BusinessCase
 */
class ItineraryRead extends BusinessCase
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ItineraryService
     */
    private $itineraryService;

    /**
     * ItineraryRead constructor.
     *
     * @param ItineraryService $service
     * @param LoggerInterface  $logger
     */
    public function __construct(ItineraryService $service, LoggerInterface $logger)
    {
        $this->itineraryService = $service;
        $this->logger           = $logger;
    }

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond()
    {
        try {
            $response = ResultResponse::fromJsonString(
                $this->itineraryService->read(
                    $this->getRequest()->headers->get('Authenticate'),
                    $this->getRequest()->headers->get('Session'),
                    $this->getRequest()->query->get('recordLocator')
                )
            );
            $this->addLinkToSelf($response);
            return $response;
        } catch (InvalidRequestParameterException $exception) {
            $this->logger->debug($exception);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($exception->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (ServiceException $exception) {
            // remarks exception handling
            $this->logger->critical($exception);
            $exception->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('itinerary', $exception);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (\Throwable $exception) {
            // general exception handling
            $this->logger->critical($exception);
            $errorException = new GeneralServerErrorException($exception->getMessage());

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
     *
     * @return HalResponse
     */
    private function addLinkToSelf(HalResponse $response) : HalResponse
    {
        return $response->addMetaData(
            [
                '_links' => [
                    'self' => ['href' => '/itinerary'],
                ],
            ]
        );
    }
}
