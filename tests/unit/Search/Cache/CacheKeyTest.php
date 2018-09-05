<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Cache;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Flight\SearchRequestMapping\Entity\BusinessCaseOptions;
use Flight\SearchRequestMapping\Entity\Leg;
use Flight\SearchRequestMapping\Entity\Request;
use Flight\Service\Amadeus\Search\Cache\CacheKey;

/**
 * CacheKeyTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Cache\CacheKey
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CacheKeyTest extends \Codeception\Test\Unit
{
    /**
     * Verify that changes in certain inputs change the cache key
     */
    public function testItChangesTheKey()
    {
        $request = new Request();
        $leg = new Leg();
        $leg->setDepartAt(new \DateTime('2017-10-18'));
        $request->setLegs(new ArrayCollection([$leg]));
        $businessCase = new BusinessCase();
        $businessCase->setAuthentication(new BusinessCaseAuthentication());
        $businessCase->setOptions(new BusinessCaseOptions());
        $config = new \stdClass();

        $allDifferent = [];

        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setFilterCabinClass(['Y']);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setFilterCabinClass(['Y', 'F']);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setFilterAirline(['AB']);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setFilterAirline(['AB', 'LH']);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setAdults(1);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setChildren(1);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setInfants(1);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $leg->setDepartAt(new \DateTime('2017-10-19'));
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $leg->setDeparture('DUS');
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $leg->setArrival('DRS');
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $leg->setIsFlexibleDate(true);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $businessCase->getAuthentication()->setOfficeId('office-id');
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $businessCase->getAuthentication()->setOfficeId('user-id');
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $businessCase->getOptions()->setResultLimit(100);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $businessCase->getOptions()->setIsBaggageInformationRequest(true);
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $config->excluded_airlines = ['AB', 'LH'];
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $config->request_options = ['ABC', 'DEF'];
        $allDifferent[] = (string) new CacheKey($request, $businessCase, $config);

        $this->assertEquals($allDifferent, array_unique($allDifferent));
    }

    /**
     * Verify that certain inputs are ignored when calculating the cache key
     */
    public function testItKeepsKeyTheSame()
    {
        $request = new Request();
        $leg = new Leg();
        $request->setLegs(new ArrayCollection([$leg]));

        $businessCase = new BusinessCase();
        $businessCase->setAuthentication(new BusinessCaseAuthentication());
        $businessCase->setOptions(new BusinessCaseOptions());
        $config = new \stdClass();

        $allIdentical = [];

        $request->setFilterCabinClass(['Y', 'F']);
        $request->setFilterFreeBaggage(false);
        $request->setFilterIdenticalOrigin(false);
        $leg->setDepartAt(new \DateTime('2017-10-18 10:00'));
        $allIdentical[] = (string) new CacheKey($request, $businessCase, $config);

        $request->setFilterCabinClass(['F', 'Y']);
        $request->setFilterStops(2);
        $request->setFilterAirport(['DUS']);
        $request->setFilterArriveBefore(['whatever']);
        $request->setFilterDepartAfter(['whatever']);
        $request->setFilterFreeBaggage(true);
        $request->setFilterIdenticalOrigin(true);
        $request->setFilterPaymentMethod(['Visa']);
        $request->setFilterPriceMax(1000.0);
        $request->setFilterPriceMax(10.0);
        $request->setAgent('fluege.de');
        $request->setDepartAt(['ignored']);
        $leg->setFilter(['whatever']);
        $leg->setDepartAt(new \DateTime('2017-10-18 20:00'));
        $allIdentical[] = (string) new CacheKey($request, $businessCase, $config);

        $this->assertCount(1, array_unique($allIdentical));
    }
}
