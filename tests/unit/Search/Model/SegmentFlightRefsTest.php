<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use AmadeusService\Search\Model\SegmentFlightRefs;
use Codeception\Util\Debug;


/**
 * SegmentFlightRefsTest.php
 *
 * @coversDefaultClass AmadeusService\Search\Model\SegmentFlightRefs
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SegmentFlightRefsTest extends \Codeception\Test\Unit
{
    /**
     * @covers ::__construct
     * @covers ::getSegmentRefNumbers
     * @covers ::indexByQualifier
     */
    public function testItExtractsSegmentRefNumbers()
    {
        $flightRefs = json_decode(json_encode(new \SimpleXMLElement('
            <recommendation>
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
            </recommendation>
        ')));

        $object = new SegmentFlightRefs($flightRefs->segmentFlightRef);

        $this->assertEquals([1, 1], $object->getSegmentRefNumbers());
    }

    /**
     * @covers ::fromRecommendation
     *
     * @dataProvider provideBuildFromRecommendationTestCases
     */
    public function testItBuildsFromRecommendationNode(string $recommendation, array $expectedSegmentNumbers)
    {
        $recommendationNode = json_decode(json_encode(new \SimpleXMLElement($recommendation)));

        $object = SegmentFlightRefs::fromRecommendation($recommendationNode);
        $this->assertEquals($expectedSegmentNumbers, $object->getSegmentRefNumbers());
    }

    public function provideBuildFromRecommendationTestCases()
    {
        return [
            'segmentFlightRef is object' => [
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
                'expected-segment-numbers' => [1, 1],
            ],

            'segmentFlightRef is array' => [
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
                'expected-segment-numbers' => [1, 1],
            ],

            'no segmentFlightRef' => [
                'recommendation' => '<recommendation />',
                'expected-segment-numbers' => [],
            ]
        ];
    }
}
