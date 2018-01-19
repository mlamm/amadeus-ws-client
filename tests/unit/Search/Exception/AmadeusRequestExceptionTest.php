<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\FlightSearch\Exception;

use Amadeus\Client\Result\NotOk;
use Codeception\Test\Unit;
use Flight\Service\Amadeus\Search\Exception\AmadeusRequestException;

/**
 * AmadeusRequestExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass Flight\Service\Amadeus\Search\Exception\AmadeusRequestException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestExceptionTest extends Unit
{
    /**
     * @covers ::getInternalErrorCode
     * @covers ::getInternalErrorMessage
     */
    public function testItAssignsExternalError() : void
    {
        $expectedMessage = 'AMADEUS RESPONSE ERROR [100,first error description], [101,second error description]';
        $expectedInternalErrorCode = 'ARS0004';

        $exception = new AmadeusRequestException([
            new NotOk(100, 'first error description'),
            new NotOk(101, 'second error description'),
        ]);

        $this->assertEquals($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
    }
}
