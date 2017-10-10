<?php
declare(strict_types=1);

namespace AmadeusService\Tests\FlightSearch\Exception;

use Codeception\Test\Unit;
use AmadeusService\Search\Exception\InvalidRequestException;

/**
 * InvalidRequestExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Exception\InvalidRequestException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class InvalidRequestExceptionTest extends Unit
{
    /**
     * @covers ::getInternalErrorCode
     * @covers ::getInternalErrorMessage
     */
    public function testItGivesBackFixedMessageAndCode() : void
    {
        $expectedMessage = 'The provided request could not be mapped into the appropriate format';
        $expectedInternalErrorCode = 'ARS0003';

        $exception = new InvalidRequestException();

        $this->assertEquals($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
    }
}
