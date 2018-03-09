<?php

namespace Flight\Service\Amadeus\Application\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ErrorLogger
 *
 * @author    Falk Woelfing <falk.woelfing@invia.de>
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ErrorLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ErrorLogger constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log exception and additional information based on status code.
     *
     * @param \Throwable $exception
     * @param Request    $request
     * @param int        $statusCode
     * @param string     $severity
     */
    public function logException(\Throwable $exception, Request $request, int $statusCode, $severity = LogLevel::WARNING)
    {
        $data = [];
        if (400 <= $statusCode) {
            $data['request']['method']  = $request->getMethod();
            $data['request']['uri']     = $request->getRequestUri();
            $data['request']['content'] = str_replace(PHP_EOL, '', $request->getContent());
        }

        if (500 <= $statusCode) {
            $data['stacktrace'] = $exception->getTraceAsString();
        }

        $this->logger->log($severity, $exception->getMessage(), $data);
    }
}