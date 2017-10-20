<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use AmadeusService\Search\Model\Nights;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Segment;

/**
 * NightsTest.php
 *
 * @covers AmadeusService\Search\Model\Nights
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class NightsTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it calculates the overnights correctly
     *
     * @dataProvider provideTestCases
     */
    public function testItCalculatesNights(array $segments, int $expectedOvernights)
    {
        $segmentEntities = (new ArrayCollection($segments))
            ->map(function (array $flightTimes) {
                $segment = new Segment();
                $segment->setDepartAt(new \DateTime($flightTimes[0]));
                $segment->setArriveAt(new \DateTime($flightTimes[1]));
                return $segment;
            });

        $this->assertSame($expectedOvernights, Nights::calc($segmentEntities));
    }

    /**
     * Provide test cases. Mostly copied from IBE
     *
     * @return array
     */
    public function provideTestCases()
    {
        return [
            [
                'segments' => [
                    ['2012-11-11', '2012-11-11']
                ],
                'expectedNights' => 0
            ],
            [
                'segments' => [
                    ['2012-11-11 00:01:00', '2012-11-11 23:55:00']
                ],
                'expectedNights' => 0
            ],
            [
                'segments' => [
                    ['2012-11-11', '2012-11-12']
                ],
                'expectedNights' => 1
            ],
            [
                'segments' => [
                    ['2012-11-11 23:50:00', '2012-11-12 00:05:00']
                ],
                'expectedNights' => 1
            ],
            [
                'segments' => [
                    ['2012-12-31', '2013-01-02']
                ],
                'expectedNights' => 2
            ],

            [
                'segments' => [
                    ['2012-11-11', '2012-11-11'],
                    ['2012-11-11', '2012-11-11'],
                ],
                'expectedNights' => 0
            ],
            [
                'segments' => [
                    ['2012-11-11 00:01:00', '2012-11-11 21:00:00'],
                    ['2012-11-11 22:00:00', '2012-11-11 23:55:00'],
                ],
                'expectedNights' => 0
            ],
            [
                'segments' => [
                    ['2012-11-11', '2012-11-11'],
                    ['2012-11-11', '2012-11-12'],
                ],
                'expectedNights' => 1
            ],
            [
                'segments' => [
                    ['2012-11-11', '2012-11-12'],
                    ['2012-11-12', '2012-11-12'],
                ],
                'expectedNights' => 1
            ],
            [
                'segments' => [
                    ['2012-11-11 23:50:00', '2012-11-12 00:05:00'],
                    ['2012-11-11 00:10:00', '2012-11-12 05:00:00'],
                ],
                'expectedNights' => 1
            ],
            [
                'segments' => [
                    ['2012-12-31', '2013-01-01'],
                    ['2013-01-01', '2013-01-02'],
                ],
                'expectedNights' => 2
            ],
        ];
    }

    /**
     * Verify that the dates in the segment are not changed during the calculation (clone!)
     */
    public function testItDoesNotChangeOriginalDates()
    {
        $segment = new Segment();
        $segment->setDepartAt(new \DateTime('2017-10-11 13:13:00'));
        $segment->setArriveAt(new \DateTime('2017-10-11 14:13:00'));

        Nights::calc(new ArrayCollection([$segment]));

        $this->assertEquals(new \DateTime('2017-10-11 13:13:00'), $segment->getDepartAt());
        $this->assertEquals(new \DateTime('2017-10-11 14:13:00'), $segment->getArriveAt());
    }

    /**
     * Verify that it handles empty segments correctly
     */
    public function testItReturnsZeroOnEmptySegments()
    {
        $this->assertSame(0, Nights::calc(new ArrayCollection()));
    }
}
