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
    const SUPPLIER_NAME     = 'amadeus';

    const CACHE_REQUEST_HIT = 'hit';
    const CACHE_REQUEST_MISS = 'miss';

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

    /**
     * Increases the cache requests by 1 either for hits or misses.
     *
     * @param string $action The target action used for the supplier request.
     * @param string $status MetricsTracker::CACHE_REQUEST_HIT or MetricsTracker::CACHE_REQUEST_MISS
     *
     * @return void
     */
    public function incrementCacheRequestCounter(string $action, string $status = self::CACHE_REQUEST_HIT)
    {
        $this->application['metrics.supplier_cache_requests_total']->incBy(1, [$status, self::SUPPLIER_NAME, $action]);
    }
}