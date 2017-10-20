<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusRequestTransformer;
use AmadeusService\Tests\Helper\RequestFaker;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Psr\Log\NullLogger;

/**
 * AmadeusRequestTransformerTest.php
 *
 * @covers \AmadeusService\Search\Model\AmadeusRequestTransformer
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestTransformerTest extends \Codeception\Test\Unit
{

    /**
     * test the main functionality of the class with a standard request
     */
    public function testItBuildsOptions()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 2, 1],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 10,
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 2;

        $expectedInfants = new Client\RequestOptions\Fare\MPPassenger();
        $expectedInfants->type = Client\RequestOptions\Fare\MPPassenger::TYPE_INFANT;
        $expectedInfants->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild,
            2 => $expectedInfants
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            ),
            1 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getReturnDepartureDateTime(),
                        ]
                    )
                ]
            )
        ];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(10, $options->nrOfRequestedResults);
        $this->assertEquals(4, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);

    }

    /**
     * tests the handling for airline filter
     */
    public function testItTransformsAirlineFilter()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'one-way',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_AIRLINE_FILTER => ['AB', 'LH']
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            )
        ];

        $expectedAF = ['M' => ['AB', 'LH']];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(3, $options->nrOfRequestedResults);
        $this->assertEquals(2, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);
        $this->assertArraySubset($expectedAF, $options->airlineOptions);


    }

    /**
     * tests if it processes the airline blacklist and transform it into the request
     *
     */
    public function testItProcessesAirlineBlacklist()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->excluded_airlines = ['DY', '3K', 'MX', 'OB'];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'one-way',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_AIRLINE_FILTER => ['AB', 'LH']
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            )
        ];

        $expectedExcludedAL = ['X' => ['DY', '3K', 'MX', 'OB']];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(3, $options->nrOfRequestedResults);
        $this->assertEquals(2, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);
        $this->assertArraySubset($expectedExcludedAL, $options->airlineOptions);
    }

    /**
     * test if it handles the cabin class filter with 2 defined values in the airline filter
     */
    public function testItTransformsCabinClassFilter()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'one-way',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            )
        ];

        $expectedCabinClass = $requestOptions[RequestFaker::OPT_CABIN_CLASS_FILTER];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(3, $options->nrOfRequestedResults);
        $this->assertEquals(2, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);
        $this->assertEquals('MD', $options->cabinOption);
        $this->assertArraySubset($expectedCabinClass, $options->cabinClass);
    }

    /**
     * test if it sets the right parts for area search
     *
     */
    public function testItSetsAreaSearchFilter()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->area_search_distance = 100;

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'one-way',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => true,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER',
                            'radiusDistance' => 100,
                            'radiusUnit' => 'K'
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON',
                            'radiusDistance' => 100,
                            'radiusUnit' => 'K'
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            )
        ];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(3, $options->nrOfRequestedResults);
        $this->assertEquals(2, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);
    }

    /**
     * test if it handles flexible date flag
     */
    public function testItSetsFlexibleDate()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->flexible_date_range = 1;

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, true],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $expectedAdult = new Client\RequestOptions\Fare\MPPassenger();
        $expectedAdult->type = Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT;
        $expectedAdult->count = 1;

        $expectedChild = new Client\RequestOptions\Fare\MPPassenger();
        $expectedChild->type = Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD;
        $expectedChild->count = 1;

        $expectedPax = [
            0 => $expectedAdult,
            1 => $expectedChild
        ];

        $expectedLegs = [
            0 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER',
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON',
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getDepartureDateTime(),
                        ]
                    )
                ]
            ),
            1 => new Client\RequestOptions\Fare\MPItinerary(
                [
                    'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'LON',
                        ]
                    ),
                    'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                        [
                            'city' => 'BER',
                        ]
                    ),
                    'date'              => new Client\RequestOptions\Fare\MPDate(
                        [
                            'dateTime' => RequestFaker::getReturnDepartureDateTime(),
                            'rangeMode' => 'C',
                            'range' => 1
                        ]
                    )
                ]
            )
        ];

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(3, $options->nrOfRequestedResults);
        $this->assertEquals(2, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);
    }

    /**
     * Verify that it creates the client params from the given business case
     */
    public function testItBuildsClientParams()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->wsdl = 'wsdl';

        $businessCase = new BusinessCase();
        $businessCase->setAuthentication(new BusinessCaseAuthentication());
        $businessCase->getAuthentication()->setDutyCode('duty-code');
        $businessCase->getAuthentication()->setOfficeId('office-id');
        $businessCase->getAuthentication()->setOrganizationId('organization-id');
        $businessCase->getAuthentication()->setPasswordData('password-data');
        $businessCase->getAuthentication()->setPasswordLength('password-length');
        $businessCase->getAuthentication()->setUserId('user-id');

        $transformer = new AmadeusRequestTransformer($config);
        $params = $transformer->buildClientParams($businessCase, new NullLogger());

        $this->assertEquals('duty-code', $params->authParams->dutyCode);
        $this->assertEquals('office-id', $params->authParams->officeId);
        $this->assertEquals('organization-id', $params->authParams->organizationId);
        $this->assertEquals('password-data', $params->authParams->passwordData);
        $this->assertEquals('password-length', $params->authParams->passwordLength);
        $this->assertEquals('user-id', $params->authParams->userId);
    }
}
