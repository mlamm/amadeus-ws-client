<?php
declare(strict_types=1);

namespace AmadeusService\Search\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcachedCache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * CacheProvider.php
 *
 * Register the various caching services
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CacheProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app) : void
    {
        $config = $app['config'];

        // register cache which does not store anything
        $app['cache.flights.void'] = function () {
            return new DoctrineFlightCache(
                new \Doctrine\Common\Cache\VoidCache(),
                0
            );
        };

        // register cache which stores in apcu (local memory)
        $app['cache.flights.apcu'] = function () use ($app, $config) {
            return new ExceptionLoggingCache(
                new CompressingFlightCache(
                    new DoctrineFlightCache(
                        new \Doctrine\Common\Cache\ApcuCache(),
                        $config->search->flight_cache->life_time
                    )
                ),
                $app['monolog']
            );
        };

        // register cache which stores on disk
        $app['cache.flights.file'] = function () use ($app, $config) {
            return new ExceptionLoggingCache(
                new CompressingFlightCache(
                    new DoctrineFlightCache(
                        new FilesystemCache('var/cache/flights'),
                        $config->search->flight_cache->life_time
                    )
                ),
                $app['monolog']
            );
        };

        // register cache which stores in memcache
        $app['cache.flights.memcache'] = function () use ($app, $config) {
            $memcachedCache = new MemcachedCache();
            $memcachedCache->setMemcached(new \Memcached('ama-flights'));
            $memcachedCache->getMemcached()->addServer(
                $config->search->flight_cache->memcache->host,
                $config->search->flight_cache->memcache->port
            );

            // use builtin compression which is quite fast
            $memcachedCache->getMemcached()->setOption(\Memcached::OPT_COMPRESSION, true);

            return new ExceptionLoggingCache(
                new DoctrineFlightCache(
                    $memcachedCache,
                    $config->search->flight_cache->life_time
                ),
                $app['monolog']
            );
        };

        // register cache which stores in redis
        $app['cache.flights.redis'] = function () use ($app, $config) {
            $redis = new \Redis();
            $redis->connect(
                $config->search->flight_cache->redis->host,
                $config->search->flight_cache->redis->port,
                1.0
            );
            $redisCache = new RedisCache();
            $redisCache->setRedis($redis);

            return new ExceptionLoggingCache(
                new CompressingFlightCache(
                    new DoctrineFlightCache(
                        $redisCache,
                        $config->search->flight_cache->life_time
                    )
                ),
                $app['monolog']
            );
        };

        // builds the cache from the adapter set in the config
        $app['cache.flights'] = function () use ($app, $config) {
            $cacheName = "cache.flights.{$config->search->flight_cache->adapter}";
            return $app[$cacheName];
        };
    }
}
