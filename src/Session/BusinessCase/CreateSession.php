<?php
namespace Flight\Service\Amadeus\Session\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Psr\Log\LoggerInterface;
use Flight\Service\Amadeus\Session\Service\Session;
use Flight\Service\Amadeus\Session\Response\ResultResponse;

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
     * @throws \Exception
     */
    public function respond()
    {
        $response = ResultResponse::fromJsonString($this->sessionService->createSession(
            $this->getRequest()->headers->get('authentication')
        ));

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
