<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Service\Amadeus\Search\Model\Carriers;


/**
 * CarriersTest.php
 *
 * @covers \Flight\Service\Amadeus\Search\Model\Carriers
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CarriersTest extends \Codeception\Test\Unit
{
    public function testItFetchesFromFlightDetail()
    {
        $flightDetail = json_decode(json_encode([
            'flightInformation' => [
                'companyId' => [
                    'operatingCarrier' => 'AA',
                    'marketingCarrier' => 'BB',
                ],
            ],
        ]));

        $companyTextIndex = new \ArrayObject();

        $segment = Carriers::writeToSegment(new SearchResponse\Segment(), $flightDetail, $companyTextIndex);

        $this->assertInstanceOf(SearchResponse\SegmentCarriers::class, $segment->getCarriers());

        $this->assertInstanceOf(SearchResponse\Carrier::class, $segment->getCarriers()->getOperating());
        $this->assertEquals('AA', $segment->getCarriers()->getOperating()->getIata());

        $this->assertInstanceOf(SearchResponse\Carrier::class, $segment->getCarriers()->getMarketing());
        $this->assertEquals('BB', $segment->getCarriers()->getMarketing()->getIata());
    }

    public function testItFetchesFromCompanyIdTexts()
    {
        $flightDetail = json_decode(json_encode([
            'flightInformation' => [
                'companyId' => [
                    'marketingCarrier' => 'BB',
                ],
            ],
            'commercialAgreement' => [
                'codeshareDetails' => [
                    'flightNumber' => '33',
                ]
            ]
        ]));

        $companyTextIndex = new \ArrayObject([
            '33' => 'AA Airlines'
        ]);

        $segment = Carriers::writeToSegment(new SearchResponse\Segment(), $flightDetail, $companyTextIndex);

        $this->assertInstanceOf(SearchResponse\SegmentCarriers::class, $segment->getCarriers());

        $this->assertInstanceOf(SearchResponse\Carrier::class, $segment->getCarriers()->getOperating());
        $this->assertEquals('', $segment->getCarriers()->getOperating()->getIata());
        $this->assertEquals('AA Airlines', $segment->getCarriers()->getOperating()->getName());

        $this->assertInstanceOf(SearchResponse\Carrier::class, $segment->getCarriers()->getMarketing());
        $this->assertEquals('BB', $segment->getCarriers()->getMarketing()->getIata());
    }
}
