<?php
declare(strict_types=1);

namespace AmadeusService\Tests\FlightSearch\Exception;

use Codeception\Test\Unit;
use AmadeusService\Search\Exception\MissingRequestParameterException;

/**
 * MissingRequestParameterExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Exception\MissingRequestParameterException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class MissingRequestParameterExceptionTest extends Unit
{
    /**
     * @covers ::getInternalErrorCode
     * @covers ::getInternalErrorMessage
     */
    public function testItGivesBackFixedMessageAndCode() : void
    {
        $expectedMessage = 'The provided search parameters do not suffice the necessary data to start a new search';
        $expectedInternalErrorCode = 'ARS0002';

        $exception = new MissingRequestParameterException();

        $this->assertEquals($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
    }
}
