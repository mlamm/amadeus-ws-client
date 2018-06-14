<?php

namespace Flight\Service\Amadeus\Session\BusinessCase;

use Amadeus\Client\Exception;
use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Session\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Application\Exception\GeneralServerErrorException;
use Flight\Service\Amadeus\Session\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Session\Response\SessionCommitResponse;
use Psr\Log\LoggerInterface;
use Flight\Service\Amadeus\Session\Service\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommitSession BusinessCase
 *
 * @package     Flight\Service\Amadeus\Session\BusinessCase
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class CommitSession extends BusinessCase
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
        $this->logger         = $logger;
    }

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond()
    {

        try {
            $authentication = $this->getRequest()->headers->get('authentication');
            $session        = $this->getRequest()->headers->get('session');

            $commit         = $this->sessionService->commitSession(
                $authentication,
                $session
            );
            $signOut        = $this->sessionService->closeSession(
                $authentication,
                $session
            );

            $responses = ['result' => $commit && $signOut];
            $response  = SessionCommitResponse::fromJsonString(json_encode($responses), Response::HTTP_NO_CONTENT);

        } catch (AmadeusRequestException $exception) {
            $this->logger->critical($exception);
            $exception->setResponseCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolation('session', $exception);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (InvalidRequestParameterException $exception) {
            $this->logger->debug($exception);

            $errorResponse = new AmadeusErrorResponse();
            $errorResponse->addViolationFromValidationFailures($exception->getFailures());
            $errorResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->addLinkToSelf($errorResponse);

            return $errorResponse;
        } catch (Exception $exception) {
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
                    'self' => ['href' => '/session'],
                ],
            ]
        );
    }
}
