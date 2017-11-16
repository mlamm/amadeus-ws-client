<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Response;

use Codeception\Test\Unit;
use Flight\Service\Amadeus\Search\Response\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * ErrorTest.php
 *
 * test the simple error class, all for the coverage
 *
 * @covers Flight\Service\Amadeus\Search\Response\Error
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ErrorTest extends Unit
{
    public function testItCreatesAnError()
    {
        $error = new Error('something', 'ARS9999', Response::HTTP_EXPECTATION_FAILED, 'iam a message');

        $this->assertEquals('something', $error->getProperty());
        $this->assertEquals('ARS9999', $error->getCode());
        $this->assertEquals(417, $error->getStatus());
        $this->assertEquals('iam a message', $error->getMessage());
    }
}
