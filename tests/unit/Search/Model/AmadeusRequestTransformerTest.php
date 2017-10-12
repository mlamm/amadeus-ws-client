<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusRequestTransformer;
use AmadeusService\Tests\Helper\RequestFaker;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseOptions;
use Flight\SearchRequestMapping\Entity\Request;

/**
 * AmadeusRequestTransformerTest.php
 *
 * @covers AmadeusService\Search\Model\AmadeusRequestTransformer
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestTransformerTest extends \Codeception\Test\Unit
{
    public function testItBuildsOptions()
    {
        $transformer = new AmadeusRequestTransformer();

        $legs = new ArrayCollection();
        $legs->add([
            'departure' => 'BER',
            'arrival'  => 'LON',
            'depart-at'=> RequestFaker::getDepartureTimestamp()
        ]);
        $legs->add([
            'departure' => 'LON',
            'arrival'  => 'BER',
            'depart-at'=> RequestFaker::getReturnDepartureTimestamp()
        ]);

        $businessCaseOptions = new BusinessCaseOptions();
        $businessCaseOptions->

        $businessCase = new BusinessCase();
        $businessCase->setOptions($businessCaseOptions);

        $request = new Request();
        $request->setLegs($legs);

        $request->setBusinessCases(new ArrayCollection());
        $request->getBusinessCases()->add(new ArrayCollection());
        $request->getBusinessCases()->first()->add();
        $request->getBusinessCases()->first()->first();

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
    }

    public function testItTransformsAirlineFilter()
    {

    }

    public function testItProcessesAirlineBlacklist()
    {

    }

    public function testItTransformsCabinClassFilter()
    {

    }
}
