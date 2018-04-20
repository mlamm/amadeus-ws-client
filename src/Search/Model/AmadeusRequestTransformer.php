<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client;
use Amadeus\Client\RequestOptions\Fare\MasterPricer\CarrierFeeDetails;
use Amadeus\Client\RequestOptions\Fare\MasterPricer\DataTypeInformation;
use Amadeus\Client\RequestOptions\Fare\MasterPricer\FeeDetails;
use Amadeus\Client\RequestOptions\Fare\MasterPricer\FeeInfo;
use Amadeus\Client\RequestOptions\Fare\MasterPricer\FeeTypeInfo;
use Amadeus\Client\RequestOptions\Fare\MPFeeOption;
use Amadeus\Client\RequestOptions\FareMasterPricerTbSearch;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Leg;
use Flight\SearchRequestMapping\Entity\Request;

/**
 * AmadeusRequestTransformer.php
 *
 * Build an Amadeus search request
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestTransformer
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }

    /**
     * transforms the request option object out of given request and adds excluded airline information if needed
     *
     * @param Request     $request
     *
     * @return FareMasterPricerTbSearch
     */
    public function buildFareMasterRequestOptions(Request $request) : FareMasterPricerTbSearch
    {
        /** @var BusinessCase $businessCase */
        $businessCase = $request->getBusinessCases()->first()->first();

        $itineraries = $this->buildItineraries($request, $businessCase);

        $coopCodes = [];
        if (!empty($this->config->search->coop_codes)) {
            $coopCodes = $this->config->search->coop_codes;
        }

        $excludedAirlines = [];
        if (!empty($this->config->search->excluded_airlines)) {
            $excludedAirlines = $this->config->search->excluded_airlines;
        }

        $options = [
            'nrOfRequestedResults' => $businessCase->getOptions()->getResultLimit(),
            'nrOfRequestedPassengers' => $request->getAdults() + $request->getChildren(),
            'passengers' => $this->setupPassengers($request),
            'itinerary' => $itineraries,
            'flightOptions' => $this->buildFlightOptions($businessCase, $coopCodes),
            'feeOption' => $this->buildFeeOption()
        ];

        if (!empty($request->getFilterAirline())) {
            $filterAirlines = array_map('strtoupper', $request->getFilterAirline());
            $options['airlineOptions'][FareMasterPricerTbSearch::AIRLINEOPT_MANDATORY] =
                array_diff($filterAirlines, $excludedAirlines);
        } elseif (!empty($excludedAirlines)) {
            $options['airlineOptions'][FareMasterPricerTbSearch::AIRLINEOPT_EXCLUDED] = $excludedAirlines;
        }

        if (!empty(($request->getFilterCabinClass()))) {
            $filterCabinClasses = array_map('strtoupper', $request->getFilterCabinClass());
            $options['cabinOption'] = FareMasterPricerTbSearch::CABINOPT_MANDATORY;
            $options['cabinClass'] = $filterCabinClasses;
        }

        if ($request->getFilterStops() === 0) {
            $options['requestedFlightTypes'] = [
                FareMasterPricerTbSearch::FLIGHTTYPE_NONSTOP,
            ];
        }

        if (!empty($coopCodes)) {
            $options['corporateQualifier'] = FareMasterPricerTbSearch::CORPORATE_QUALIFIER_UNIFARE;
            $options['corporateCodesUnifares'] = array_values($coopCodes);

        }

        return new FareMasterPricerTbSearch($options);
    }

    /**
     * build the itinerary part of the request object
     *
     * @param Request $request
     * @param BusinessCase $businessCase
     *
     * @return array
     */
    private function buildItineraries(Request $request, BusinessCase $businessCase) : array
    {
        $itineraries = [];
        $areaSearchEnabled = $businessCase->getOptions()->isAreaSearch();

        /** @var Leg $leg */
        foreach ($request->getLegs() as $leg) {
            $itineraryOptions = $this->buildItineraryOptions($leg, $areaSearchEnabled, (bool) $leg->getIsFlexibleDate());
            $itineraries[] = new Client\RequestOptions\Fare\MPItinerary(
                $itineraryOptions
            );
        }

        return $itineraries;
    }

    /**
     * Method to setup passengers to request for based on sent Request object
     * @param Request $request
     *
     * @return Client\RequestOptions\Fare\MPPassenger[]
     */
    private function setupPassengers(Request $request) : array
    {
        $passengers = [];

        if ($request->getAdults() > 0) {
            $passengers[] = new Client\RequestOptions\Fare\MPPassenger(
                [
                    'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_ADULT,
                    'count' => $request->getAdults()
                ]
            );
        }

        if ($request->getChildren() > 0) {
            $passengers[] = new Client\RequestOptions\Fare\MPPassenger(
                [
                    'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_CHILD,
                    'count' => $request->getChildren()
                ]
            );
        }

        if ($request->getInfants() > 0) {
            $passengers[] = new Client\RequestOptions\Fare\MPPassenger(
                [
                    'type' => Client\RequestOptions\Fare\MPPassenger::TYPE_INFANT,
                    'count' => $request->getInfants()
                ]
            );
        }

        return $passengers;
    }

    /**
     * builds options array for leg adds area search information if needed
     *
     * @param Leg  $leg
     * @param bool $isAreaSearch
     * @param bool $isFlexibleDate
     *
     * @return array
     */
    private function buildItineraryOptions(Leg $leg, bool $isAreaSearch, bool $isFlexibleDate) : array
    {
        $options =  [
            'departureLocation' => new Client\RequestOptions\Fare\MPLocation(
                [
                    'city' => $leg->getDeparture(),
                ]
            ),
            'arrivalLocation'   => new Client\RequestOptions\Fare\MPLocation(
                [
                    'city' => $leg->getArrival()
                ]
            ),
            'date'              => new Client\RequestOptions\Fare\MPDate(
                [
                    'dateTime' => $leg->getDepartAt(),
                ]
            )
        ];

        if ($isAreaSearch) {
            $options['arrivalLocation']->radiusDistance = $options['departureLocation']->radiusDistance = $this->config->search->area_search_distance;
            $options['arrivalLocation']->radiusUnit = $options['departureLocation']->radiusUnit = Client\RequestOptions\Fare\MPLocation::RADIUSUNIT_KILOMETERS;
        }

        if ($isFlexibleDate) {
            $options['date']->rangeMode = Client\RequestOptions\Fare\MPDate::RANGEMODE_PLUS;
            $options['date']->range     = $this->config->search->flexible_date_range;
        }

        return $options;
    }

    /**
     * builds FlightOption array out of request and config settings
     *
     * @param BusinessCase $businessCase
     * @param array|null   $coopCodes
     *
     * @return array
     */
    protected function buildFlightOptions(BusinessCase $businessCase, array $coopCodes) : array
    {
        $pricingOptions = [];
        if (!empty($this->config->search->request_options)) {
            $pricingOptions = $this->config->search->request_options;
        }

        $overnightOptions = [];
        if (!empty($this->config->search->overnight_options)) {
            $overnightOptions = $this->config->search->overnight_options;
        }

        //removes CorpUnifare option if no CoopCode is set in config
        if (empty($coopCodes)) {
            $pricingOptions = array_diff($pricingOptions, [FareMasterPricerTbSearch::FLIGHTOPT_CORPORATE_UNIFARES]);
        }

        if ($businessCase->getOptions()->isOvernight()) {
            $pricingOptions = array_merge($pricingOptions, $overnightOptions);
        }

        if ($businessCase->getOptions()->isBaggageInformationRequest()) {
            $pricingOptions = array_merge($pricingOptions, $this->config->search->bag_option);
        }

        return $pricingOptions;
    }

    /**
     * builds feeOption request
     *
     * @return array
     */
    protected function buildFeeOption() :array
    {
        $feeOption = [
            new MPFeeOption([
                'feeTypeInfo' => new FeeTypeInfo([
                        'carrierFeeDetails' => new CarrierFeeDetails([
                            'type' => CarrierFeeDetails::TYPE_TICKETING_FEES
                        ])
                    ]
                ),
                'feeDetails'  => [
                    new FeeDetails([
                        'feeInfo' => new FeeInfo([
                            'dataTypeInformation' => new DataTypeInformation([
                                'subType' => DataTypeInformation::SUB_TYPE_FARE_COMPONENT_AMOUNT,
                                'option'  => DataTypeInformation::OPTION_MANUALLY_INCLUDED
                            ])
                        ])
                    ])
                ]
            ])
        ];

        return $feeOption;
    }
}
