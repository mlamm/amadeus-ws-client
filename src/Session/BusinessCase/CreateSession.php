<?php
namespace Flight\Service\Amadeus\Session\BusinessCase;

use Amadeus\Client\Exception;
use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Session\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Session\Exception\InvalidRequestException;
use Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Session\Response\AmadeusErrorResponse;
use Psr\Log\LoggerInterface;
use Flight\Service\Amadeus\Session\Service\Session;
use Flight\Service\Amadeus\Session\Response\SessionCreateResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateSession BusinessCase
 *
 * @package Flight\Service\Amadeus\Session\BusinessCase
 */
class CreateSession extends BusinessCase
{
    /**
     * @var \Flight\Service\Amadeus\Session\Service\Session
     */
    protected $sessionService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Session         $session
     * @param LoggerInterface $logger
     */
    public function __construct(Session $session, LoggerInterface $logger)
    {
        $this->sessionService = $session;
        $this->logger = $logger;
    }

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond()
    {

        try {
            $response = SessionCreateResponse::fromJsonString(
                $this->sessionService->createSession(
                    $this->getRequest()->headers->get('authentication')
                )
            );

        } catch (AmadeusRequestException $e) {
            $this->logger->critical($e);
            $e->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('session', $e);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (InvalidRequestParameterException $e) {
            $this->logger->debug($e);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($e->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (InvalidRequestException $e) {
            $this->logger->debug($e);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('_', $e);
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (Exception $e) {
            // general exception handling
            $this->logger->critical($e);
            $errorException = new GeneralServerErrorException($e->getMessage());

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('_', $errorException);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        }

        $this->addLinkToSelf($response);
        return $response;
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
                'self' => ['href' => '/session']
            ]
        ]);
    }
}
