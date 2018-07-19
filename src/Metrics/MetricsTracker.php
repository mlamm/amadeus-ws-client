<?php

namespace Flight\Service\Amadeus\Metrics;

use Pimple\Container;

/**
 * Tracks metric data.
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    t.sari <tibor.sari@invia.de>
 */
class MetricsTracker
{
    const SUPPLIER_NAME = 'amadeus';

    /**
     * @var Container
     */
    private $application;

    /**
     * MetricsTracker constructor.
     *
     * @param Container $application
     */
    public function __construct(Container $application)
    {
        $this->application = $application;
    }

    /**
     * Logs the (almost) net response time of amadeus request.
     *
     * @param float  $duration
     * @param string $action The target action used for the supplier request.
     * @param int    $statusCode Http status code
     *
     * @return void
     */
    public function logResponseLatency(float $duration, string $action, int $statusCode = 200)
    {
        $this->application['metrics.supplier_response_time_seconds']->observe(
            $duration,
            [
                $statusCode,
                self::SUPPLIER_NAME,
                $action
            ]
        );
    }
}