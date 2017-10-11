<?php
declare(strict_types=1);

namespace AmadeusService\Tests\FlightSearch\Exception;

use Codeception\Test\Unit;
use AmadeusService\Search\Exception\AmadeusRequestException;

/**
 * AmadeusRequestExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Exception\AmadeusRequestException
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
        //prepareError
        $text = 'something is wrong';
        $code = 99;

        $expectedMessage = 'AMADEUS RESPONSE ERROR -- 101 -- first error description, second error description';
        $expectedInternalErrorCode = 'ARS000X';

        $error = new \stdClass();
        $error->applicationError = new \stdClass();
        $error->applicationError->applicationErrorDetail = new \stdClass();
        $error->applicationError->applicationErrorDetail->error = 101;
        $error->errorMessageText = new \stdClass();
        $error->errorMessageText->description = [
            'first error description',
            'second error description'
        ];

        $exception = new AmadeusRequestException($text, $code);
        $exception->assignError($error);

        $this->assertEquals($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
    }
}
