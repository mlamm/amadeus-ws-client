<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Cache;

use Doctrine\Common\Cache\Cache as DoctrineCache;

/**
 * DoctrineFlightCache.php
 *
 * Allows the use of a doctrine cache backend as a flight cache
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class DoctrineFlightCache implements FlightCacheInterface
{
    /**
     * @var int
     */
    private $lifeTime;

    /**
     * @var DoctrineCache
     */
    private $cache;

    /**
     * @param DoctrineCache $cache
     * @param int           $lifeTime
     */
    public function __construct(DoctrineCache $cache, int $lifeTime)
    {
        $this->cache = $cache;
        $this->lifeTime = $lifeTime;
    }

    /**
     * @param string $cacheKey
     * @return mixed
     */
    public function fetch(string $cacheKey)
    {
        return $this->cache->fetch($cacheKey);
    }

    /**
     * @param string $cacheKey
     * @param string $data
     * @return bool
     */
    public function save(string $cacheKey, string $data) : bool
    {
        return $this->cache->save($cacheKey, $data, $this->lifeTime);
    }
}
