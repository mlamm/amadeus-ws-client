<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use AmadeusService\Search\Model\FlightProposals;

/**
 * FlightProposalsTest.php
 *
 * @coversDefaultClass AmadeusService\Search\Model\FlightProposals
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FlightProposalsTest extends \Codeception\Test\Unit
{
    /**
     * @covers ::__construct
     * @covers ::hasMajorityCarrier
     * @covers ::getMajorityCarrier
     */
    public function testItReturnsMainCarrier()
    {
        $object = new FlightProposals([
            (object) [
                'ref' => '4U',
                'unitQualifier' => 'MCX',
            ]
        ]);

        $this->assertTrue($object->hasMajorityCarrier());
        $this->assertEquals('4U', $object->getMajorityCarrier());

        $object = new FlightProposals([]);
        $this->assertFalse($object->hasMajorityCarrier());
    }

    /**
     * @covers ::hasElapsedFlyingTime
     * @covers ::getElapsedFlyingTime
     */
    public function testItReturnsEstimatedFlightTime()
    {
        $object = new FlightProposals([
            (object) [
                'ref' => '0205', // 2 hours and 5 minutes
                'unitQualifier' => 'EFT',
            ]
        ]);

        $this->assertTrue($object->hasElapsedFlyingTime());
        $this->assertEquals(2 * 60 * 60 + 5 * 60, $object->getElapsedFlyingTime());

        $object = new FlightProposals([]);
        $this->assertFalse($object->hasElapsedFlyingTime());
    }

    /**
     * @covers ::fromGroupOfFlights
     */
    public function testItBuildsFromGroupOfFlights()
    {
        // array of flightProposal nodes
        $object = FlightProposals::fromGroupOfFlights((object) [
            'propFlightGrDetail' => (object) [
                'flightProposal' => [
                    (object) [
                        'ref' => '0205',
                        'unitQualifier' => 'EFT',
                    ],
                    (object) [
                        'ref' => '4U',
                        'unitQualifier' => 'MCX',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(FlightProposals::class, $object);
        $this->assertTrue($object->hasMajorityCarrier());

        // only one flightProposal node, no wrapping array
        $object = FlightProposals::fromGroupOfFlights((object) [
            'propFlightGrDetail' => (object) [
                'flightProposal' =>
                    (object) [
                        'ref' => '4U',
                        'unitQualifier' => 'MCX',
                ],
            ],
        ]);

        $this->assertInstanceOf(FlightProposals::class, $object);
        $this->assertTrue($object->hasMajorityCarrier());

        $object = FlightProposals::fromGroupOfFlights((object) []);
        $this->assertInstanceOf(FlightProposals::class, $object);
        $this->assertFalse($object->hasMajorityCarrier());
    }
}
