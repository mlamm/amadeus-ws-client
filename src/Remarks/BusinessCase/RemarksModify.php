<?php
namespace Flight\Service\Amadeus\Remarks\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Remarks\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;
use Flight\Service\Amadeus\Remarks\Service\Remarks;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * businesscase for remarks modify
 *
 * @package Flight\Service\Amadeus\Remarks\BusinessCase
 */
class RemarksModify extends BusinessCase
{
    /**
     * @var \Flight\Service\Amadeus\Remarks\Service\Remarks
     */
    protected $remarksService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \Flight\Service\Amadeus\Remarks\Service\Remarks $remarksService
     * @param LoggerInterface                       $logger
     */
    public function __construct(Remarks $remarksService, LoggerInterface $logger)
    {
        $this->remarksService = $remarksService;
        $this->logger = $logger;
    }

    /**
     * @return HalResponse
     */
    public function respond() : HalResponse
    {
        try {
            $response = ResultResponse::fromJsonString($this->remarksService->remarksModify(
                $this->getRequest()->headers->get('authentication'),
                $this->getRequest()->getContent()
            ));

            $this->addLinkToSelf($response);
            return $response;
        } catch (InvalidRequestParameterException $ex) {
            $this->logger->debug($ex);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($ex->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (ServiceException $ex) {
            // remarks exception handling
            $this->logger->critical($ex);
            $ex->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('remarks', $ex);
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
                'self' => ['href' => '/remarks']
            ]
        ]);
    }
}
