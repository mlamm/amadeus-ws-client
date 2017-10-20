<?php
declare(strict_types=1);

namespace AmadeusService\Search\Cache;

/**
 * RedisCache.php
 *
 * Overwritten to disable the serializer (we are storing binary data in the cache, no need to serialize)
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class RedisCache extends \Doctrine\Common\Cache\RedisCache
{
    protected function getSerializerValue() : int
    {
        return \Redis::SERIALIZER_NONE;
    }
}
