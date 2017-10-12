<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Response;

use AmadeusService\Search\Response\AmadeusErrorResponse;
use Codeception\Test\Unit;
use Particle\Validator\Failure;

/**
 * AmadeusErrorResponseTest.php
 *
 * test functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Response\AmadeusErrorResponse
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusErrorResponseTest extends Unit
{
    /**
     * @covers ::addViolationFromValidationFailures
     * @covers ::addMetaData
     */
    public function testItCreatesValidErrorMessage()
    {
        /** @var Failure[] $failures */
        $failures = [
            new Failure('first', '', 'something is wrong with first', []),
            new Failure('second','','another failure in second', [])
        ];

        $response = new AmadeusErrorResponse;
        $response->addViolationFromValidationFailures($failures);
        $response->addMetaData([
            '_links' => [
                'self' => ['href' => '/flight-search/']
            ]
        ]);

        $decodedResponse = json_decode($response->getContent(), true);
        $expectedErrorSubset = [
            'first' => [
                0 => [
                    'code' => 'ARS0004',
                    'message' => 'INVALID OR MISSING REQUEST PARAM - something is wrong with first',
                    'status' => 400
                ]
            ],
            'second' => [
                0 => [
                    'code' => 'ARS0004',
                    'message' => 'INVALID OR MISSING REQUEST PARAM - another failure in second',
                    'status' => 400
                ]
            ]
        ];

        $this->assertArrayHasKey('errors', $decodedResponse);
        $this->assertArraySubset($expectedErrorSubset, $decodedResponse['errors']);
    }
}
