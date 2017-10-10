<?php
declare(strict_types=1);

namespace AmadeusService\Tests\FlightSearch\Exception;

use Codeception\Test\Unit;
use AmadeusService\Search\Exception\ValidationException;

/**
 * ValidationExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Exception\ValidationException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ValidationExceptionTest extends Unit
{
    /**
     * @covers ::getInternalErrorCode
     * @covers ::getInternalErrorMessage
     */
    public function testItGivesBackFixedMessageAndCode() : void
    {
        $expectedMessage = 'INVALID OR MISSING REQUEST PARAM - this param is invalid';
        $expectedInternalErrorCode = 'ARS0004';

        $exception = new ValidationException('this param is invalid');
        $exception->setResponseCode(404);


        $this->assertEquals($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
        $this->assertEquals(404, $exception->getResponseCode());
    }
}
