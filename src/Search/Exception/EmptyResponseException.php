<?php

namespace Flight\Service\Amadeus\Search\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

/**
 * There was an amadeus error but we want to deliver an http 200 with empty result instead.
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    t.sari <tibor.sari@invia.de>
 */
class EmptyResponseException extends AmadeusRequestException
{

    /**
     * Level name of the log message caused by this exception
     *
     * @var string
     */
    protected $logSeverity;

    /**
     * EmptyResponseException constructor.
     *
     * @param array  $messages
     * @param int    $statusCode
     * @param string $logSeverity
     */
    public function __construct(array $messages, int $statusCode = Response::HTTP_BAD_REQUEST, string $logSeverity = LogLevel::WARNING)
    {
        parent::__construct($messages);

        $this->responseCode = $statusCode;
        $this->logSeverity = $logSeverity;
    }

    /**
     * Getter of LogSeverity.
     *
     * @return string
     */
    public function getLogSeverity(): string
    {
        return $this->logSeverity;
    }

    /**
     * Setter of LogSeverity.
     *
     * @param string $logSeverity
     *
     * @return EmptyResponseException
     */
    public function setLogSeverity(string $logSeverity): EmptyResponseException
    {
        $this->logSeverity = $logSeverity;

        return $this;
    }


}