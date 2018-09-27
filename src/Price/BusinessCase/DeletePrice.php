<?php

namespace Flight\Service\Amadeus\Price\BusinessCase;

use Amadeus\Client\Exception;
use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Price\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Price\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Price\Response\PriceDeleteResponse;
use Psr\Log\LoggerInterface;
use Flight\Service\Amadeus\Price\Service\Price;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DeletePrice BusinessCase
 *
 * @package Flight\Service\Amadeus\Price\BusinessCase
 */
class DeletePrice extends BusinessCase
{
    /**
     * @var \Flight\Service\Amadeus\Price\Service\Price
     */
    protected $priceService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Price           $Price
     * @param LoggerInterface $logger
     */
    public function __construct(Price $Price, LoggerInterface $logger)
    {
        $this->priceService = $Price;
        $this->logger       = $logger;
    }

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond() : HalResponse
    {

        try {
            $response = PriceDeleteResponse::fromJsonString(
                $this->priceService->deletePrice(
                    $this->getRequest()->headers->get('authentication'),
                    $this->getRequest()->headers->get('session')
                ),
                Response::HTTP_NO_CONTENT
            );

        } catch (AmadeusRequestException $exception) {
            // its ok if no tst exist
            if ($this->noTstExist($exception->getInternalErrorMessage())) {
                return new PriceDeleteResponse(null, Response::HTTP_NO_CONTENT);
            }
            $this->logger->critical($exception);
            $exception->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('Price', $exception);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (InvalidRequestParameterException $exception) {
            $this->logger->debug($exception);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($exception->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (\Exception $exception) {
            // general exception handling
            $this->logger->critical($exception);
            $errorException = new GeneralServerErrorException($exception->getMessage());

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('_', $errorException);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        }

        return $response;
    }

    /**
     * check if no tst exist
     *
     * @param $message
     * @return bool
     */
    protected function noTstExist($message)
    {
        if ('AMADEUS RESPONSE ERROR [2102,NEED TST]' == $message) {
            return true;
        }
        return false;
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
                    'self' => ['href' => '/price'],
                ],
            ]
        );
    }
}
