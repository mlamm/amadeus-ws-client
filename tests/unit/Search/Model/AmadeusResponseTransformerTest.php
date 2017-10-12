<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use AmadeusService\Search\Model\AmadeusResponseTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;

/**
 * AmadeusResponseTransformerTest.php
 *
 * @covers AmadeusService\Search\Model\AmadeusResponseTransformer
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusResponseTransformerTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it generates the expected output from the amadeus response
     *
     *
     * @dataProvider provideTestCases
     */
    public function testItTransforms(string $amaResponseFile, string $itinType, string $expectedSearchResponseFile)
    {
        $amaResponse = file_get_contents(codecept_data_dir($amaResponseFile));

        $amaResult = new Result(new SendResult());
        $amaResult->responseXml = $amaResponse;
        $amaResult->response = json_decode(json_encode(new \SimpleXMLElement($amaResponse)));

        $searchRequest = new Request();
        $searchRequest->setBusinessCases(new ArrayCollection());
        $searchRequest->getBusinessCases()->add(new ArrayCollection());
        $searchRequest->getBusinessCases()->first()->add(new BusinessCase());
        $searchRequest->getBusinessCases()->first()->first()->setType($itinType);

        $transformer = new AmadeusResponseTransformer(new Mapper());
        $response = $transformer->mapResultToDefinedStructure($searchRequest, $amaResult);

        $this->assertEquals(
            json_decode(file_get_contents(codecept_data_dir($expectedSearchResponseFile)), true),
            json_decode($transformer->getMappedResponseAsJson($response), true)
        );
    }

    /**
     * @return array
     */
    public function provideTestCases()
    {
        return [
            'one-way' => [
                'ama-response'            => 'fixtures/01-masterPricer-response-oneway.xml',
                'type'                    => 'one-way',
                'expected-searchresponse' => 'fixtures/01-searchresponse-oneway.json',
            ],

            'round-trip' => [
                'ama-response'            => 'fixtures/02-masterPricer-response-roundtrip.xml',
                'type'                    => 'round-trip',
                'expected-searchresponse' => 'fixtures/02-searchresponse-roundtrip.json',
            ],
        ];
    }
}
