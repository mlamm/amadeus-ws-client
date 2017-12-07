<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Flight\Service\Amadeus\Search\Cache\CompressingFlightCache;
use Flight\Service\Amadeus\Search\Cache\DoctrineFlightCache;
use Flight\Service\Amadeus\Search\Cache\FlightCacheInterface;

/**
 * CompressingFlightCacheTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Cache\CompressingFlightCache
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CompressingFlightCacheTest extends \Codeception\Test\Unit
{
    /**
     * Verify that the data passed to the decorated cache is smaller in size
     */
    public function testItReducesSize()
    {
        $uncompressedData = file_get_contents(codecept_data_dir('fixtures/01-masterPricer-response-oneway.xml'));

        $decorated = $this->getMockBuilder(FlightCacheInterface::class)->getMock();
        $decorated
            ->expects($this->once())
            ->method('save')
            ->with($this->anything(), $this->callback(function ($compressedData) use ($uncompressedData) {
                $this->assertLessThan(strlen($uncompressedData), strlen($compressedData));
                return true;
            }));

        $object = new CompressingFlightCache($decorated);

        $object->save('key', $uncompressedData);
    }

    /**
     * Verify that the compression is reversed on fetch
     */
    public function testItReversesTheCompression()
    {
        $uncompressedData = file_get_contents(codecept_data_dir('fixtures/01-masterPricer-response-oneway.xml'));

        $object = new CompressingFlightCache(new DoctrineFlightCache(new ArrayCache(), 3600));
        $object->save('key', $uncompressedData);
        $this->assertEquals($uncompressedData, $object->fetch('key'));
    }
}
