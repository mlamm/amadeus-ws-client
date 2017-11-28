<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Flight\Service\Amadeus\Search\Cache\DoctrineFlightCache;
use Flight\Service\Amadeus\Search\Cache\ExceptionLoggingCache;
use Flight\Service\Amadeus\Search\Cache\FlightCacheInterface;
use Gamez\Psr\Log\TestLogger;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * ExceptionLoggingCacheTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Cache\ExceptionLoggingCache
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ExceptionLoggingCacheTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it calls the decorated cache and passes the values
     */
    public function testItPassesToDecoratedCache()
    {
        $decorated = new ArrayCache();
        $object = new ExceptionLoggingCache(new DoctrineFlightCache($decorated, 3600), new NullLogger());

        $object->save('key', 'value');
        $this->assertEquals('value', $object->fetch('key'));
        $this->assertFalse($object->fetch('non-existent'));
    }

    /**
     * Verify that it suppresses and logs exceptions on fetch
     */
    public function testItLogsExceptionOnFetch()
    {
        $decorated = $this->getMockBuilder(FlightCacheInterface::class)->getMock();

        $decorated
            ->expects($this->once())
            ->method('fetch')
            ->willThrowException(new \Exception('any exception'));

        $logger = new TestLogger();

        $object = new ExceptionLoggingCache($decorated, $logger);

        $result = $object->fetch('key');
        $this->assertFalse($result);

        $this->assertCount(1, $logger->log);
        $this->assertEquals(LogLevel::ERROR, $logger->log[0]->level);
    }

    /**
     * Verify that it suppresses and logs exceptions on save
     */
    public function testItLogsExceptionOnSave()
    {
        $decorated = $this->getMockBuilder(FlightCacheInterface::class)->getMock();

        $decorated
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('any exception'));

        $logger = new TestLogger();

        $object = new ExceptionLoggingCache($decorated, $logger);

        $result = $object->save('key', 'value');
        $this->assertFalse($result);

        $this->assertCount(1, $logger->log);
        $this->assertEquals(LogLevel::ERROR, $logger->log[0]->level);
    }
}
