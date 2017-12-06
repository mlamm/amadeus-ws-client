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
    public static function load(bool $cachingEnabled, string $cacheFile, callable $createCallback)
    {
        if (file_exists($cacheFile)) {
            $config = unserialize(file_get_contents($cacheFile));

            if ($cachingEnabled) {
                return $config;
            }
        }

        $config = self::buildConfig($createCallback);
        file_put_contents($cacheFile, serialize($config));

        return $config;
    }

    /**
     * Verify the return type of the callback
     *
     * @param callable $createCallback
     * @return \stdClass
     */
    private static function buildConfig(callable $createCallback): \stdClass
    {
        return $createCallback();
    }
}
