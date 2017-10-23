<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\SearchRequestMapping\Entity\BusinessCase;

/**
 * AmadeusResponseTransformerTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer
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
     * @dataProvider provideTestCases
     */
    public function testItTransforms(string $amaResponseFile, string $itinType, int $limit, string $expectedSearchResponseFile)
    {
        $amaResponse = file_get_contents(codecept_data_dir($amaResponseFile));

        $amaResult = new Result(new SendResult());
        $amaResult->responseXml = $amaResponse;
        $amaResult->response = json_decode(json_encode(new \SimpleXMLElement($amaResponse)));

        $businessCase = new BusinessCase();
        $businessCase->setType($itinType);

        $transformer = new AmadeusResponseTransformer();
        $response = $transformer->mapResultToDefinedStructure($businessCase, $amaResult);

        // limit response from fixture since we do not have expected values for all flights
        $response->setResult(new ArrayCollection($response->getResult()->slice(0, $limit)));

        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $serializedResponse = $serializer->serialize($response, 'json');

        $this->assertEquals(
            json_decode(file_get_contents(codecept_data_dir($expectedSearchResponseFile)), true),
            json_decode($serializedResponse, true)
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
                'limit'                   => 1,
                'expected-searchresponse' => 'fixtures/01-searchresponse-oneway.json',
            ],

            'round-trip' => [
                'ama-response'            => 'fixtures/02-masterPricer-response-roundtrip.xml',
                'type'                    => 'round-trip',
                'limit'                   => 1,
                'expected-searchresponse' => 'fixtures/02-searchresponse-roundtrip.json',
            ],

            'free baggage' => [
                'ama-response'            => 'fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt.xml',
                'type'                    => 'round-trip',
                'limit'                   => 1,
                'expected-searchresponse' => 'fixtures/03-searchresponse-FBA-rt.json',
            ],
        ];
    }
}
