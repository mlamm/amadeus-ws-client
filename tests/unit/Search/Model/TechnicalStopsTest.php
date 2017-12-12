<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Segment;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\TechnicalStop;
use Flight\Service\Amadeus\Search\Model\TechnicalStops;

/**
 * TechnicalStopsTest.php
 *
 * @covers Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\TechnicalStop
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class TechnicalStopsTest extends \Codeception\Test\Unit
{
    /**
     * Does it convert it input structure?
     */
    public function testItConverts()
    {
        $flightDetails = json_decode(json_encode(new \SimpleXMLElement('
            <flightDetails>
                <technicalStop>
                    <stopDetails>
                        <dateQualifier>AA</dateQualifier>
                        <date>190412</date>
                        <firstTime>0952</firstTime>
                        <locationId>YQB</locationId>
                    </stopDetails>
                    <stopDetails>
                        <dateQualifier>AD</dateQualifier>
                        <date>190412</date>
                        <firstTime>1020</firstTime>
                    </stopDetails>
                </technicalStop>
                <technicalStop>
                    <stopDetails>
                        <dateQualifier>AA</dateQualifier>
                        <date>190412</date>
                        <firstTime>1150</firstTime>
                        <locationId>YGP</locationId>
                    </stopDetails>
                    <stopDetails>
                        <dateQualifier>AD</dateQualifier>
                        <date>190412</date>
                        <firstTime>1210</firstTime>
                    </stopDetails>
                </technicalStop>
            </flightDetails>
        ')));

        $segment = TechnicalStops::writeToSegment(new Segment(), $flightDetails);

        $this->assertNotNull($segment->getTechnicalStops());
        $this->assertCount(2, $segment->getTechnicalStops());

        /** @var TechnicalStop $firstStop */
        $firstStop = $segment->getTechnicalStops()->first();
        $this->assertInstanceOf(TechnicalStop::class, $firstStop);
        /** @var TechnicalStop $secondStop */
        $secondStop = $segment->getTechnicalStops()->next();
        $this->assertInstanceOf(TechnicalStop::class, $secondStop);

        $this->assertEquals($firstStop->getArriveAt(), new \DateTime('2012-04-19 09:52'));
        $this->assertEquals($firstStop->getDepartAt(), new \DateTime('2012-04-19 10:20'));
        $this->assertNotNull($firstStop->getAirport());
        $this->assertEquals('YQB', $firstStop->getAirport()->getIata());

        $this->assertEquals($secondStop->getArriveAt(), new \DateTime('2012-04-19 11:50'));
        $this->assertEquals($secondStop->getDepartAt(), new \DateTime('2012-04-19 12:10'));
        $this->assertNotNull($secondStop->getAirport());
        $this->assertEquals('YGP', $secondStop->getAirport()->getIata());
    }
}
