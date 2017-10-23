<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Cache;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * ExceptionLoggingCache.php
 *
 * Decorator for a flight cache which suppresses exceptions and logs them
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ExceptionLoggingCache implements FlightCacheInterface
{
    /**
     * The decorated cache
     *
     * @var FlightCacheInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $logLevel = LogLevel::ERROR;

    /**
     * @param FlightCacheInterface $cache
     * @param LoggerInterface      $logger
     */
    public function __construct(FlightCacheInterface $cache, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Fetch data from the decorated cache. Exceptions are caught and logged.
     *
     * @param string $cacheKey
     * @return mixed
     */
    public function fetch(string $cacheKey)
    {
        try {
            return $this->cache->fetch($cacheKey);
        } catch (\Throwable $e) {
            $this->logger->log($this->logLevel, $e);
        }

        return false;
    }

    /**
     * Save the data to the decorated cache. Exceptions are caught and logged.
     *
     * @param string $cacheKey
     * @param string $data
     * @return bool
     */
    public function save(string $cacheKey, string $data) : bool
    {
        try {
            return $this->cache->save($cacheKey, $data);
        } catch (\Throwable $e) {
            $this->logger->log($this->logLevel, $e);
        }

        return false;
    }
}
