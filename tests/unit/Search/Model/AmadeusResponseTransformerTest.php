<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request;
use Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer;

/**
 * AmadeusResponseTransformerTest.php
 *
 * @covers \Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer
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
     *
     * @param string $amaResponseFile
     * @param string $itinType
     * @param int    $limit
     * @param string $expectedSearchResponseFile
     */
    public function testItTransforms(string $amaResponseFile, string $itinType, int $limit, string $expectedSearchResponseFile)
    {
        $amaResponse = file_get_contents(codecept_data_dir($amaResponseFile));

        $amaResult = new Result(new SendResult());
        $amaResult->responseXml = $amaResponse;

        $responseAsXmlObject = new \SimpleXMLElement($amaResponse);
        $xmlNameSpace = array_values($responseAsXmlObject->getNamespaces())[0];
        $responseAsXmlObject->registerXPathNamespace('ns', $xmlNameSpace);

        $amaResult->response = json_decode(json_encode($responseAsXmlObject));

        $businessCase = new BusinessCase();
        $businessCase->setType($itinType);
        $businessCase->setContentProvider('amadeus');

        $transformer = new AmadeusResponseTransformer();

        $request = new Request();

        $paxReference = $responseAsXmlObject->xpath('./ns:recommendation/ns:paxFareProduct/ns:paxReference[ns:ptc="ADT"]');
        if (false === empty($paxReference)) {
            $request->setAdults(count($paxReference[0]->traveller));
        }
        $paxReference = $responseAsXmlObject->xpath('./ns:recommendation/ns:paxFareProduct/ns:paxReference[ns:ptc="CH"]');
        if (false === empty($paxReference)) {
            $request->setChildren(count($paxReference[0]->traveller));
        }
        $paxReference = $responseAsXmlObject->xpath('./ns:recommendation/ns:paxFareProduct/ns:paxReference[ns:ptc="INF"]');
        if (false === empty($paxReference)) {
            $request->setInfants(count($paxReference[0]->traveller));
        }

        $response = $transformer->mapResultToDefinedStructure($businessCase, $request, $amaResult);

        // limit response from fixture since we do not have expected values for all flights
        $response->setResult(new ArrayCollection($response->getResult()->slice(0, $limit)));

        $mapper = new Mapper();
        $serializedResponse = $mapper->createJson($response);

        $this->tester->canSeeJsonStringIsValidOnSchema($serializedResponse, codecept_data_dir('schema/response-schema.json'));

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

            'technical stops' => [
                'ama-response'            => 'fixtures/04-Fare_MasterPricerTravelBoardSearch_TechnicalStop.xml',
                'type'                    => 'round-trip',
                'limit'                   => 1,
                'expected-searchresponse' => 'fixtures/04-searchresponse-technical-stops.json',
            ],

            'baggage fees' => [
                'ama-response'            => 'fixtures/05-Fare_MasterPricerTravelBoardSearch_BaggageFee.xml',
                'type'                    => 'round-trip',
                'limit'                   => 1,
                'expected-searchresponse' => 'fixtures/05-searchresponse-baggagefee.json'
            ]
        ];
    }
}
