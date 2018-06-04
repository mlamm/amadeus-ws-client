<?php
namespace Flight\Service\Amadeus\Session\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Psr\Log\LoggerInterface;
use Flight\Service\Amadeus\Session\Service\Session;
use Flight\Service\Amadeus\Session\Response\ResultResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateSession BusinessCase
 *
 * @package Flight\Service\Amadeus\Session\BusinessCase
 */
class IgnoreSession extends BusinessCase
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
     * @param Session $remarksService
     * @param LoggerInterface                       $logger
     */
    public function __construct(Session $remarksService, LoggerInterface $logger)
    {
        $this->sessionService = $remarksService;
        $this->logger = $logger;
    }

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond()
    {
        $response = ResultResponse::fromJsonString($this->sessionService->ignoreSession(
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
