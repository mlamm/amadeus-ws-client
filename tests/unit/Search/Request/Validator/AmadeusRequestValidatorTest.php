<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Request\Validator;

use AmadeusService\Search\Exception\InvalidRequestParameterException;
use AmadeusService\Search\Request\Validator\AmadeusRequestValidator;
use Codeception\Test\Unit;

/**
 * AmadeusRequestValidatorTest.php
 *
 * tests the main function of the class
 *
 * @covers AmadeusService\Search\Request\Validator\AmadeusRequestValidator
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestValidatorTest extends Unit
{
    public function testValidateCorrectRequestFunction() : void
    {
        $requestContent = file_get_contents(codecept_data_dir('requests/valid-request.json'));

        $config = new \stdClass();
        $config->allowed_agents      = ['fluege.de'];
        $config->allowed_types       = ['round-trip', 'one-way', 'open-jaw'];
        $config->allowed_cabin_class = ['Y', 'B', 'F'];

        $validator = new AmadeusRequestValidator($config);
        $validator->validateRequest($requestContent);
    }

    /**
     * @param array $requestContent
     * @param array $expected
     *
     * @dataProvider provideFailValidationData
     */
    public function testItThrowsOnValidationError(array $requestContent, array $expected) : void
    {
        $config = new \stdClass();
        $config->allowed_agents = ['fluege.de'];
        $config->allowed_types = ['round-trip', 'one-way', 'open-jaw'];
        $config->allowed_cabin_class = ['Y', 'B', 'F'];

        $validator = new AmadeusRequestValidator($config);

        $this->expectException($expected['exceptionClass']);
        $validator->validateRequest($requestContent['rawJson']);
    }

    /**
     * provides request data for test case
     *
     * @return array
     */
    public function provideFailValidationData() : array
    {
        return [
            'testWithNotAllowedAgent' => [
                'requestData' => [
                    'rawJson' => file_get_contents(codecept_data_dir('requests/invalid-agent-request.json')),
                ],
                'expected' => [
                    'exceptionClass' => InvalidRequestParameterException::class
                ]
            ]
        ];
    }
}
