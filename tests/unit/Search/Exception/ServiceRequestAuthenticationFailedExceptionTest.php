<?php
declare(strict_types=1);

namespace AmadeusService\Tests\FlightSearch\Exception;

use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use Codeception\Test\Unit;

/**
 * ServiceRequestAuthenticationFailedExceptionTest.php
 *
 * Tests the functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ServiceRequestAuthenticationFailedExceptionTest extends Unit
{
    /**
     * @covers ::getInternalErrorCode
     * @covers ::getInternalErrorMessage
     */
    public function testItGivesBackFixedMessageAndCode() : void
    {
        $expectedMessage = 'The `Amadeus\Client::securityAuthenticate` method didn\'t return state OK';
        $expectedInternalErrorCode = 'ARS0001';

        $exception = new ServiceRequestAuthenticationFailedException([]);

        $this->assertStringStartsWith($expectedMessage, $exception->getInternalErrorMessage());
        $this->assertEquals($expectedInternalErrorCode, $exception->getInternalErrorCode());
    }
}
