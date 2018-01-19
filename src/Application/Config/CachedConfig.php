<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Application\Config;

/**
 * CachedConfig.php
 *
 * Cache the return value of a callback into a file
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CachedConfig
{
    /**
     * Load from cache or create via callback
     *
     * @param bool     $cachingEnabled
     * @param string   $cacheFile
     * @param callable $createCallback
     * @return mixed|\stdClass
     */
    public static function load(bool $cachingEnabled, string $cacheFile, callable $createCallback): \stdClass
    {
        if (file_exists($cacheFile)) {
            $config = unserialize(file_get_contents($cacheFile));

            if ($cachingEnabled) {
                return $config;
            }
        }

        $config = $createCallback();
        file_put_contents($cacheFile, serialize($config));

        return $config;
    }
}
