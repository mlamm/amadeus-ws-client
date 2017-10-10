<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use AmadeusService\Search\Model\AmadeusResponseTransformer;

/**
 * AmadeusResponseTransformerTest.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusResponseTransformerTest extends \Codeception\Test\Unit
{
    /**
     * @covers AmadeusService\Search\Model\AmadeusResponseTransformer
     *
     * @dataProvider provideTestCases
     */
    public function testItTransforms(string $amaResponseFile, string $expectedSearchResponseFile)
    {
        $amaResponse = file_get_contents(codecept_data_dir($amaResponseFile));

        $amaResult = new Result(new SendResult());
        $amaResult->responseXml = $amaResponse;
        $amaResult->response = json_decode(json_encode(new \SimpleXMLElement($amaResponse)));

        $transformer = new AmadeusResponseTransformer($amaResult);

        $this->assertEquals(
            json_decode(file_get_contents(codecept_data_dir($expectedSearchResponseFile)), true),
            json_decode($transformer->getMappedResponseAsJson(), true)
        );
    }

    public function provideTestCases()
    {
        return [
            'one-way' => [
                'ama-response'            => 'fixtures/01-masterPricer-response-oneway.xml',
                'expected-searchresponse' => 'fixtures/01-searchresponse-oneway.json',
            ],

            'round-trip' => [
                'ama-response'            => 'fixtures/02-masterPricer-response-roundtrip.xml',
                'expected-searchresponse' => 'fixtures/02-searchresponse-roundtrip.json',
            ],
        ];
    }
}
