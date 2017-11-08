<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer;
use Flight\Service\Amadeus\Tests\Helper\RequestFaker;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Psr\Log\NullLogger;

/**
 * AmadeusRequestTransformerTest.php
 *
 * @covers \Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer
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
    }

    /**
     * tests the handling for airline filter
     */
    public function testItTransformsAirlineFilter()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->excluded_airlines = ['DY'];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_AIRLINE_FILTER => ['ab', 'lh', 'dy']
        ];
        $request = RequestFaker::buildDefaultRequest($requestOptions);

        $expectedAF = ['M' => ['AB', 'LH']];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
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

        $request = RequestFaker::buildDefaultRequest();

        $expectedExcludedAL = ['X' => ['DY', '3K', 'MX', 'OB']];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
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
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['y', 'c'],
        ];
        $request = RequestFaker::buildDefaultRequest($requestOptions);

        $expectedCabinClass = ['Y', 'C'];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals('MD', $options->cabinOption);
        $this->assertEquals($expectedCabinClass, $options->cabinClass);
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
            RequestFaker::OPT_AREA_SEARCH => true,
        ];
        $request = RequestFaker::buildDefaultRequest($requestOptions);

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

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertArraySubset($expectedLegs, $options->itinerary);
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
        ];
        $request = RequestFaker::buildDefaultRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

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
        $this->assertArraySubset($expectedLegs, $options->itinerary);
    }

    /**
     * test it handles main FlightOptions set in config
     */
    public function testItAddsBaseRequestOptions()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->flexible_date_range = 1;
        $config->search->request_options = [
            'A',
            'B',
            'C'
        ];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, true],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
            RequestFaker::OPT_IS_OVERNIGHT => false
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $expectedFlightOptions = ['A', 'B', 'C'];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertArraySubset($expectedFlightOptions, $options->flightOptions);
    }

    /**
     * test it handles main FlightOptions and adds coop codes
     */
    public function testItAddsCoopCodes()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->flexible_date_range = 1;
        $config->search->request_options = [
            Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES,
            'B',
            'C'
        ];
        $config->search->coop_codes = [
            '1234',
            '5678',
            '9ABC'
        ];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, true],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
            RequestFaker::OPT_IS_OVERNIGHT => false
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $expectedFlightOptions = [Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES, 'B',  'C'];
        $expectedCoopQualifier = Client\RequestOptions\FareMasterPricerTbSearch::CORPORATE_QUALIFIER_UNIFARE;
        $expectedCoopCodes     = ['1234', '5678', '9ABC'];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertArraySubset($expectedFlightOptions, $options->flightOptions);
        $this->assertEquals($expectedCoopQualifier, $options->corporateQualifier);
        $this->assertArraySubset($expectedCoopCodes, $options->corporateCodesUnifares);
    }

    /**
     * test it handles main FlightOptions and dont adds coop code data and remove UNIFARE Option
     */
    public function testRemovesUnifareOptionWithNoCoopCode()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->flexible_date_range = 1;
        $config->search->request_options = [
            Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES,
            'B',
            'C'
        ];
        $config->search->coop_codes = [
        ];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, true],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
            RequestFaker::OPT_IS_OVERNIGHT => false
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $expectedFlightOptions = [1 => 'B', 2 => 'C'];
        $coopQualifier = Client\RequestOptions\FareMasterPricerTbSearch::CORPORATE_QUALIFIER_UNIFARE;

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertArraySubset($expectedFlightOptions, $options->flightOptions);
        $this->assertNotContains($coopQualifier, $options->flightOptions);
        $this->assertEmpty($options->corporateQualifier);
        $this->assertEmpty($options->corporateCodesUnifares);

    }

    /**
     * test it handles main FlightOptions and extends them with overnight options
     */
    public function testItExtendsOvernightOptions()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->flexible_date_range = 1;
        $config->search->request_options = [
            Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES,
            'B',
            'C'
        ];
        $config->search->coop_codes = [
            '1234',
            '5678',
            '9ABC'
        ];

        $config->search->overnight_options = [
            'X',
            'Y'
        ];

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_TYPE =>'round-trip',
            RequestFaker::OPT_FLEXIBLE_LEG_DATES => [false, true],
            RequestFaker::OPT_PAX => [1, 1, 0],
            RequestFaker::OPT_AREA_SEARCH => false,
            RequestFaker::OPT_RESULT_LIMIT => 3,
            RequestFaker::OPT_CABIN_CLASS_FILTER => ['Y', 'C'],
            RequestFaker::OPT_IS_OVERNIGHT => true
        ];
        $request = RequestFaker::getFakeRequest($requestOptions);

        $expectedFlightOptions = [Client\RequestOptions\FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES, 'B', 'C', 'X', 'Y'];
        $expectedCoopQualifier = Client\RequestOptions\FareMasterPricerTbSearch::CORPORATE_QUALIFIER_UNIFARE;
        $expectedCoopCodes     = ['1234', '5678', '9ABC'];

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertArraySubset($expectedFlightOptions, $options->flightOptions);
        $this->assertEquals($expectedCoopQualifier, $options->corporateQualifier);
        $this->assertArraySubset($expectedCoopCodes, $options->corporateCodesUnifares);
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

    /**
     * Verify that it adds the required flight types if the stops filter is set
     */
    public function testItSetsNonStopFilter()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();

        $transformer = new AmadeusRequestTransformer($config);

        $requestOptions = [
            RequestFaker::OPT_NONSTOP_FILTER => true,
        ];
        $request = RequestFaker::buildDefaultRequest($requestOptions);

        $options = $transformer->buildFareMasterRequestOptions($request);

        $this->assertInstanceOf(Client\RequestOptions\FareMasterPricerTbSearch::class, $options);
        $this->assertEquals(['N'], $options->requestedFlightTypes);
    }
}
