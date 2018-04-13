<?php

namespace Flight\Service\Amadeus\Application\Logger;

/**
 * Class TracingHeader
 *
 * @author    Friedemann Schmuhl <friedemann.schmuhl@invia.de>
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 */
class TracingHeader
{
    const TRACING_HEADER_NAME = 'X-Cloud-Trace-Context';

    /**
     * @var string
     */
    protected $traceId = '';

    /**
     * @var int
     */
    protected $spanId = 0;

    /**
     * Create TracingHeader object by given parent tracing header
     * If no $tracingHeaderRequest is given a new one will be generated
     *
     * @param null|string $tracingHeaderRequest
     */
    public function __construct(?string $tracingHeaderRequest = null)
    {
        if (false === $this->parseHeader($tracingHeaderRequest)) {
            $this->createNewTracingHeader();
        }
    }

    /**
     * The getter function for the property <em>$traceId</em>.
     *
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * The getter function for the property <em>$spanId</em>.
     *
     * @return int
     */
    public function getSpanId(): int
    {
        return $this->spanId;
    }

    /**
     * Create the tracing header for a sub request
     *
     * @return string
     */
    public function createTracingHeader(): string
    {
        return $this->traceId . '/' . ($this->spanId + 1);
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function processLogRecord(array $record): array
    {
        $record['trace'] = [
            'id'   => $this->traceId,
            'span' => $this->spanId
        ];

        return $record;
    }

    /**
     * Parse the given tracing header and save the results
     *
     * @param null|string $tracingHeader
     *
     * @return bool
     */
    protected function parseHeader(?string $tracingHeader): bool
    {
        $parseHeader = false;
        if (preg_match('@^(?<traceId>[a-z0-9\-]+)/(?<spanId>\d+)@i', $tracingHeader, $matches)) {
            $this->traceId = $matches['traceId'];
            $this->spanId  = (int)$matches['spanId'];
            $parseHeader   = true;
        }

        return $parseHeader;
    }

    /**
     * Create a new tracing header
     *
     * @return $this
     */
    protected function createNewTracingHeader(): TracingHeader
    {
        $this->traceId = md5(openssl_random_pseudo_bytes(32));
        $this->spanId  = 0;

        return $this;
    }
}
