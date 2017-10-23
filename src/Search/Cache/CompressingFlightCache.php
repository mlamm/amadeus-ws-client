<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Cache;

/**
 * CompressingFlightCache.php
 *
 * Decorator for a flight cache which compresses the data before storage.
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CompressingFlightCache implements FlightCacheInterface
{
    /**
     * The decorated cache
     *
     * @var FlightCacheInterface
     */
    private $cache;

    /**
     * Needs to be a balance between speed and compressed size
     *
     * @var int
     */
    private $compressionLevel = 3;

    /**
     * @param FlightCacheInterface $cache
     */
    public function __construct(FlightCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetch data from the decorated cache and uncompress
     *
     * @param string $cacheKey
     * @return bool|string
     */
    public function fetch(string $cacheKey)
    {
        $compressed = $this->cache->fetch($cacheKey);

        if ($compressed === false) {
            return false;
        }

        $data = gzuncompress($compressed);

        if ($data === false) {
            throw new \RuntimeException('error while uncompressing data from cache');
        }

        return $data;
    }

    /**
     * Compress the data and store in the decorated cache
     *
     * @param string $cacheKey
     * @param string $data
     *
     * @return mixed
     */
    public function save(string $cacheKey, string $data) : bool
    {
        $compressed = gzcompress($data, $this->compressionLevel);
        return $this->cache->save($cacheKey, $compressed);
    }
}
