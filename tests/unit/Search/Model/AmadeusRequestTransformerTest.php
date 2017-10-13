<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusRequestTransformer;
use AmadeusService\Tests\Helper\RequestFaker;


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
    public function testItBuildsOptions()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, false],
            RequestFaker::OPT_PAX => [1, 2, 0],
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
        $this->assertEquals(3, $options->nrOfRequestedPassengers);
        $this->assertArraySubset($expectedPax, $options->passengers);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
        $this->assertArraySubset(['ET'], $options->flightOptions);

    }

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
}
