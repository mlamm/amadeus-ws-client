<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Exception;

use Codeception\Test\Unit;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException;
use Particle\Validator\Failure;

/**
 * InvalidRequestParamExceptionTest.php
 *
 * @coversDefaultClass Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class InvalidRequestParameterExceptionTest extends Unit
{
    /**
     * Verify that it makes the validation failures accessible
     *
     * @covers ::__construct
     * @covers ::getFailures
     * @covers ::getInternalErrorCode
     */
    public function testItStoresFailures()
    {
        $failures = [
            new Failure('property-1', 'reason-1', 'template-1', ['param-1' => 'value']),
            new Failure('property-2', 'reason-2', 'template-2', ['param-2' => 'value']),
        ];
        $exception = new InvalidRequestParameterException($failures);

        $this->assertEquals($failures, $exception->getFailures());
        $this->assertEquals('ARS0002', $exception->getInternalErrorCode());
        $this->assertEquals('INVALID OR MISSING REQUEST PARAM', $exception->getInternalErrorMessage());
    }
}