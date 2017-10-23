<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Service\Amadeus\Search\Model\SegmentFlightRefs;

/**
 * SegmentFlightRefsTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\SegmentFlightRefs
 * @covers Flight\Service\Amadeus\Search\Model\SegmentFlightRef
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SegmentFlightRefsTest extends \Codeception\Test\Unit
{
    /**
     * Verify that the index returns the correct values
     *
     * @dataProvider provideBuildFromRecommendationTestCases
     */
    public function testItBuildsFlightIndex(string $recommendation, array $expectedSegmentsPerFlight)
    {
        $recommendationNode = json_decode(json_encode(new \SimpleXMLElement($recommendation)));

        $object = SegmentFlightRefs::fromRecommendation($recommendationNode);

        $flights = $object->getSegmentRefsForFlights();

        $this->assertCount(count($expectedSegmentsPerFlight), $flights);

        foreach ($expectedSegmentsPerFlight as $index => $expectedSegments) {
            $this->assertArrayHasKey($index, $flights);
            $this->assertEquals($expectedSegments, $flights[$index]->getSegmentRefNumbers());
        }
    }

    public function provideBuildFromRecommendationTestCases()
    {
        return [
            'single flight, two legs' => [
                'recommendation' =>
                    '<recommendation>
                        <segmentFlightRef>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                        </segmentFlightRef>
                    </recommendation>',
                'expected-segments-per-flight' => [
                    [1, 1], // first flight
                ],
            ],

            'single flight, two legs, first must be ignored' => [
                'recommendation' =>
                    '<recommendation>
                        <segmentFlightRef>
                            <referencingDetail>
                                <refQualifier>C</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                        </segmentFlightRef>
                        <segmentFlightRef>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                        </segmentFlightRef>
                    </recommendation>',
                'expected-segments-per-flight' => [
                    [1, 1], // first flight
                ],
            ],

            'no segmentFlightRef' => [
                'recommendation' => '<recommendation />',
                'expected-segments-per-flight' => [],
            ],

            'multiple flights, each with two legs' => [
                'recommendation' =>
                    '<recommendation>
                        <segmentFlightRef>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>B</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                        </segmentFlightRef>
                        <segmentFlightRef>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>2</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>S</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                            <referencingDetail>
                                <refQualifier>B</refQualifier>
                                <refNumber>1</refNumber>
                            </referencingDetail>
                        </segmentFlightRef>
                    </recommendation>',
                'expected-segments-per-flight' => [
                    [1, 1], // first flight
                    [2, 1], // second flight
                ],
            ],
        ];
    }
}
