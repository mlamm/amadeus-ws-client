<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Application\Middleware;

use Flight\Service\Amadeus\Application\Middleware\JsonEncodingOptions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * JsonEncodingOptionsTest.php
 *
 * @covers Symfony\Component\HttpFoundation\JsonResponse
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class JsonEncodingOptionsTest extends \Codeception\Test\Unit
{
    /**
     * Does it convert the option strings in the config to their integer values?
     */
    public function testItSetsOptionsFromConstantStrings()
    {
        $config = (object) [
            'response' => (object) [
                'json_encoding_options' => [
                    'JSON_PRETTY_PRINT',
                    'JSON_UNESCAPED_SLASHES',
                ]
            ],
        ];

        $response = new JsonResponse();
        $object = new JsonEncodingOptions($config);

        $this->assertNotEquals(JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES, $response->getEncodingOptions());
        $object(new Request(), $response);
        $response->getEncodingOptions();
        $this->assertEquals(JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES, $response->getEncodingOptions());
    }

    /**
     * Does it throw an exception if the configured constants do not exist?
     */
    public function testItThrowsOnInvalidConfig()
    {
        $config = (object) [
            'response' => (object) [
                'json_encoding_options' => [
                    'XXX',
                    'JSON_UNESCAPED_SLASHES',
                ]
            ],
        ];

        $response = new JsonResponse();
        $object = new JsonEncodingOptions($config);

        $this->expectException(\InvalidArgumentException::class);
        $object(new Request(), $response);
    }
}
