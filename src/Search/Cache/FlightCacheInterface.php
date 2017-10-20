<?php

namespace AmadeusService\Search\Cache;

/**
 * FlightCacheInterface.php
 *
 * Simple interface to store the result of a search query.
 *
 * This abstracts away the implementation of the cache. There can be adapters for any kind of cache backend.
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
interface FlightCacheInterface
{
    /**
     * Fetch a flight search result from the cache
     *
     * @param string $cacheKey
     * @return string
     */
    public function fetch(string $cacheKey);

    /**
     * Store a flight search result in the cache
     *
     * @param string $cacheKey
     * @param string $data
     * @return bool
     */
    public function save(string $cacheKey, string $data) : bool;
}
