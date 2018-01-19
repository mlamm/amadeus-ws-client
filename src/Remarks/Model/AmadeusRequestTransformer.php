<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

use Amadeus\Client;
use Amadeus\Client\RequestOptions\FareMasterPricerTbRemarks;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Request\Entity\Authenticate;
use Psr\Log\LoggerInterface;

/**
 * AmadeusRequestTransformer.php
 *
 * Build an Amadeus remarks request
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
     * AmadeusRequestTransformer constructor.
     *
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }

    /**
     * builds the client
     *
     * @param BusinessCase $businessCase
     *
     * @return RemarksAmadeusClient
     */
    public function buildClientParams(Authenticate $authentication, LoggerInterface $logger) : Client\Params
    {

        return new Client\Params(
            [
                'authParams' => [
                    'officeId' => $authentication->getOfficeId(),
                    'userId' => $authentication->getUserId(),
                    'passwordData' => $authentication->getPasswordData(),
                    'passwordLength' => $authentication->getPasswordLength(),
                    'dutyCode' => $authentication->getDutyCode(),
                    'organizationId' => $authentication->getOrganizationId()
                ],
                'sessionHandlerParams' => [
                    'soapHeaderVersion' => Client::HEADER_V4,
                    'stateful' => false,
                    'wsdl' => "./wsdl/{$this->config->remarks->wsdl}",
                    'logger' => $logger
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'service.remarks'
                ]
            ]
        );
    }

    public function buildOptionsRemarksRead($recordlocator)
    {
        return new Client\RequestOptions\PnrRetrieveOptions(['recordLocator' => $recordlocator]);
    }

    public function buildOptionsRemarksAdd($recordlocator, ArrayCollection $remarks)
    {
        $elements = [];
        /** @var Remark $remark */
        foreach ($remarks as $remark) {
            $elements[] = (new \Amadeus\Client\RequestOptions\Pnr\Element\MiscellaneousRemark([
                'type'     => $remark->getType() ? $remark->getType() : 'RM',
                'text'     => $remark->getName() . '-' . $remark->getValue(),
                'category' => '*'
            ]));
        }

        return new Client\RequestOptions\PnrAddMultiElementsOptions([
            'recordLocator' => $recordlocator,
            'actionCode'    => 11,
            //'receivedFrom'  => 'service.remarks',
            'elements'      => $elements
        ]);
//
//        /**
//         * One or more action codes to be performed on the PNR
//         *
//         * self::ACTION_* How to handle the PNR after creating
//         *
//         * 0 No special processing
//         * 10 End transact (ET)
//         * 11 End transact with retrieve (ER)
//         * 12 End transact and change advice codes (ETK)
//         * 13 End transact with retrieve and change advice codes (ERK)
//         * 14 End transact split PNR (EF)
//         * 15 Cancel the itinerary for all PNRs connected by the AXR and end transact (ETX)
//         * 16 Cancel the itinerary for all PNRs connected by the AXR and end transact with retrieve (ERX)
//         * 20 Ignore (IG)
//         * 21 Ignore and retrieve (IR)
//         * 267 Stop EOT if segment sell error
//         * 30 Show warnings at first EOT
//         * 50 Reply with short message
//         *
//         * @var int|int[]
//         */
//        public $actionCode = 0;
//
//        /**
//         * Received From (RF) string to be added to the transaction.
//         *
//         * @var string
//         */
//        public $receivedFrom;
//
//        /**
//         * Whether to automatically add the default Received From string if none is provided.
//         *
//         * Defaults to true for backwards compatibility.
//         *
//         * See https://github.com/amabnl/amadeus-ws-client/issues/68
//         *
//         * @var bool
//         */
//        public $autoAddReceivedFrom = true;
//
//        /**
//         * A group of travellers
//         *
//         * @var Pnr\TravellerGroup
//         */
//        public $travellerGroup;
//
//        /**
//         * Non-group travellers (max 9)
//         *
//         * Will be added to the existing PNR
//         *
//         * (travellerInfo)
//         *
//         * @var Pnr\Traveller[]
//         */
//        public $travellers = [];
//
//        /**
//         * (originDestinationDetails)
//         *
//         * WARNING: IMPLIES NO CONNECTED FLIGHTS, USE $this->itinerary instead!
//         *
//         * @deprecated use $this->itinerary instead
//         * @var Pnr\Segment[]
//         */
//        public $tripSegments = [];
//
//        /**
//         * Itineraries in the PNR.
//         *
//         * Used for grouping segments together
//         *
//         * @var Pnr\Itinerary[]
//         */
//        public $itineraries = [];
//
//        /**
//         * (dataElementsMaster\dataElementsIndiv)
//         *
//         * @var Pnr\Element[]
//         */
//        public $elements = [];
    }

    public function buildOptionsRemarksDelete($recordlocator, ArrayCollection $remarks)
    {
        $elements = [];
        /** @var Remark $remark */

        foreach ($remarks as $remark) {
            $elements[] = $remark->getManagementData()->getReference()->getNumber();
        }

        return new Client\RequestOptions\PnrCancelOptions([
            'recordLocator'    => $recordlocator,
            'actionCode'       => '11',
            'elementsByTattoo' => $elements
        ]);

//        /**
//         * Only provide the Record Locator if the PNR is not yet in context!!
//         *
//         * @var string
//         */
//        public $recordLocator;
//
//        /**
//         * How to handle the PNR after doing the Cancel operation
//         *
//         * 0 No special processing
//         * 10 End transact (ET)
//         * 11 End transact with retrieve (ER)
//         * 12 End transact and change advice codes (ETK)
//         * 13 End transact with retrieve and change advice codes (ERK)
//         * 14 End transact split PNR (EF)
//         * 15 Cancel the itinerary for all PNRs connected by the AXR and end transact (ETX)
//         * 16 Cancel the itinerary for all PNRs connected by the AXR and end transact with retrieve (ERX)
//         * 20 Ignore (IG)
//         * 21 Ignore and retrieve (IR)
//         * 267 Stop EOT if segment sell error
//         * 30 Show warnings at first EOT
//         * 50 Reply with short message
//         *
//         * @var int|int[]
//         */
//        public $actionCode = 0;
//
//        /**
//         * All Passengers by name element number to be removed
//         *
//         * @var int[]
//         */
//        public $passengers = [];
//
//        /**
//         * All elements by Tattoo number to be removed
//         *
//         * @var int[]
//         */
//        public $elementsByTattoo = [];
//          tattoo number is qualifier number
//        /**
//         * Set to true if you want to cancel the entire itinerary of the PNR.
//         *
//         * This is the equivalent of the XI entry in SEL and will effectively cancel the PNR.
//         *
//         * @var bool
//         */
//        public $cancelItinerary = false;
//
//        /**
//         * Offers by Offer Reference to be removed
//         *
//         * @var int[]
//         */
//        public $offers = [];
//
//        /**
//         * All GROUP Passengers by name element number to be removed
//         *
//         * @var int[]
//         */
//        public $groupPassengers = [];
//
//        /**
//         * All tattoos of PNR Segments to be removed
//         *
//         * @var int[]
//         */
//        public $segments = [];
    }

    /**
     * transforms the request option object out of given request and adds excluded airline information if needed
     *
     * @param Request     $request
     *
     * @return FareMasterPricerTbRemarks
     */
    public function buildFareMasterRequestOptions(Request $request) : Client\RequestOptions\FareMasterPricerTbSearch
    {
        /** @var BusinessCase $businessCase */
        $businessCase = $request->getBusinessCases()->first()->first();

        $itineraries = $this->buildItineraries($request, $businessCase);

        $coopCodes = [];
        if (!empty($this->config->remarks->coop_codes)) {
            $coopCodes = $this->config->remarks->coop_codes;
        }

        $excludedAirlines = [];
        if (!empty($this->config->remarks->excluded_airlines)) {
            $excludedAirlines = $this->config->remarks->excluded_airlines;
        }

        $options = [
            'nrOfRequestedResults' => $businessCase->getOptions()->getResultLimit(),
            'nrOfRequestedPassengers' => $request->getPassengerCount(),
            'passengers' => $this->setupPassengers($request),
            'itinerary' => $itineraries,
            'flightOptions' => $this->buildFlightOptions($businessCase, $coopCodes)
        ];

        if (!empty($request->getFilterAirline())) {
            $filterAirlines = array_map('strtoupper', $request->getFilterAirline());
            $options['airlineOptions'][FareMasterPricerTbRemarks::AIRLINEOPT_MANDATORY] =
                array_diff($filterAirlines, $excludedAirlines);
        } elseif (!empty($excludedAirlines)) {
            $options['airlineOptions'][FareMasterPricerTbRemarks::AIRLINEOPT_EXCLUDED] = $excludedAirlines;
        }

        if (!empty(($request->getFilterCabinClass()))) {
            $filterCabinClasses = array_map('strtoupper', $request->getFilterCabinClass());
            $options['cabinOption'] = FareMasterPricerTbRemarks::CABINOPT_MANDATORY;
            $options['cabinClass'] = $filterCabinClasses;
        }

        if ($request->getFilterStops() === 0) {
            $options['requestedFlightTypes'] = [
                FareMasterPricerTbRemarks::FLIGHTTYPE_NONSTOP,
            ];
        }

        if (!empty($coopCodes)) {
            $options['corporateQualifier'] = FareMasterPricerTbRemarks::CORPORATE_QUALIFIER_UNIFARE;
            $options['corporateCodesUnifares'] = array_values($coopCodes);

        }

        return new FareMasterPricerTbRemarks($options);
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
        $areaRemarksEnabled = $businessCase->getOptions()->IsAreaRemarks();

        /** @var Leg $leg */
        foreach ($request->getLegs() as $leg) {
            $itineraryOptions = $this->buildItineraryOptions($leg, $areaRemarksEnabled, (bool) $leg->getIsFlexibleDate());
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
     * builds options array for leg adds area remarks information if needed
     *
     * @param Leg  $leg
     * @param bool $isAreaRemarks
     * @param bool $isFlexibleDate
     *
     * @return array
     */
    private function buildItineraryOptions(Leg $leg, bool $isAreaRemarks, bool $isFlexibleDate) : array
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

        if ($isAreaRemarks) {
            $options['arrivalLocation']->radiusDistance = $options['departureLocation']->radiusDistance = $this->config->remarks->area_remarks_distance;
            $options['arrivalLocation']->radiusUnit = $options['departureLocation']->radiusUnit = Client\RequestOptions\Fare\MPLocation::RADIUSUNIT_KILOMETERS;
        }

        if ($isFlexibleDate) {
            $options['date']->rangeMode = Client\RequestOptions\Fare\MPDate::RANGEMODE_MINUS_PLUS;
            $options['date']->range = $this->config->remarks->flexible_date_range;
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
        if (!empty($this->config->remarks->request_options)) {
            $pricingOptions = $this->config->remarks->request_options;
        }

        $overnightOptions = [];
        if (!empty($this->config->remarks->overnight_options)) {
            $overnightOptions = $this->config->remarks->overnight_options;
        }

        //removes CorpUnifare option if no CoopCode is set in config
        if (empty($coopCodes)) {
            $pricingOptions = array_diff($pricingOptions, [FareMasterPricerTbRemarks::FLIGHTOPT_CORPORATE_UNIFARES]);
        }

        if ($businessCase->getOptions()->isOvernight()) {
            $pricingOptions = array_merge($pricingOptions, $overnightOptions);
        }

        return $pricingOptions;
    }


}
