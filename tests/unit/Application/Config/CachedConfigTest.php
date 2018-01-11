<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Application\Config;

use Flight\Service\Amadeus\Application\Config\CachedConfig;

/**
 * CachedConfigTest.php
 *
 * @covers Flight\Service\Amadeus\Application\Config\CachedConfig
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CachedConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    private $cacheFile;

    protected function _before()
    {
        $this->cacheFile = codecept_output_dir('config_cache_' . random_int(0, PHP_INT_MAX));
    }

    protected function _after()
    {
        @unlink($this->cacheFile);
    }

    /**
     * Does it return the config from the callback if the cache does not exist?
     */
    public function testItCreatesFromCallbackIfNoCachedFile()
    {
        $expectedConfig = new \stdClass();

        $this->assertFileNotExists($this->cacheFile);

        $config = CachedConfig::load(
            true,
            $this->cacheFile,
            function () use ($expectedConfig) {
                return $expectedConfig;
            }
        );

        $this->assertSame($expectedConfig, $config);
    }

    /**
     * Does it return the config from the cache if the cache is enabled and the file exists?
     */
    public function testItLoadsFromCache()
    {
        $expectedConfig = new \stdClass();
        $expectedConfig->iAm = 'config';

        CachedConfig::load(
            true,
            $this->cacheFile,
            function () use ($expectedConfig) {
                return $expectedConfig;
            }
        );

        $this->assertFileExists($this->cacheFile);

        $config = CachedConfig::load(
            true,
            $this->cacheFile,
            function () use ($expectedConfig) {
                return $expectedConfig;
            }
        );

        $this->assertNotSame($expectedConfig, $config);
        $this->assertEquals($expectedConfig, $config);

    }

    /**
     * Does it ignore the cache file if caching is disabled?
     */
    public function testItCreatesFromCallbackIfCachingDisabled()
    {
        $expectedConfig = new \stdClass();

        CachedConfig::load(
            true,
            $this->cacheFile,
            function () use ($expectedConfig) {
                return $expectedConfig;
            }
        );

        $this->assertFileExists($this->cacheFile);

        $config = CachedConfig::load(
            false,
            $this->cacheFile,
            function () use ($expectedConfig) {
                return $expectedConfig;
            }
        );

        $this->assertSame($expectedConfig, $config);
    }

    /**
     * Does it throw an exception if the callback generates an invalid type?
     */
    public function testItOnlyAcceptsStdclass()
    {
        $this->expectException(\TypeError::class);

        CachedConfig::load(
            true,
            $this->cacheFile,
            function () {
                return [];
            }
        );
    }
}
