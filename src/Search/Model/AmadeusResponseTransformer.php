<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;
use AmadeusService\Console\Command\AddBusinessCase;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;

/**
 * Class AmadeusResponseTransformer
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseTransformer
{
    const SEGMENT_REF_QUALIFIER = 'S';

    const PTC_CHILD = 'CH';

    const PTC_ADULT = 'ADT';

    /**
     * @var Result
     */
    protected $amadeusResult;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var SearchResponse
     */
    private $mappedResponse;

    /**
     * Contains the flight index setup in entites
     * @var ArrayCollection
     */
    private $legCollection;

    /**
     * AmadeusResponseTransformer constructor.
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->amadeusResult = $result;
        $this->mapper = new Mapper(getcwd() . '/var/cache/response-mapping/');
        $this->mapResultToDefinedStructure();
    }


    private function mapResultToDefinedStructure()
    {
        /** @var SearchResponse $searchResponse */
        $searchResponse = new SearchResponse();
        $searchResponse->setResult(new ArrayCollection());

        $offers = $this->amadeusResult->response->recommendation;
        if (!is_array($offers)) {
            $offers = [$offers];
        }

        // map the flight index to a leg collection the first layer represents the requested leg
        // the second layer the indexed legs for said requested leg
        $this->legCollection = $this->mapLegs();

        // iterate through the recommendations
        foreach ($offers as $offerIndex => $offer) {
            $result = new SearchResponse\Result();
            $requestedLegs = new ArrayCollection();

            // iterate through the flight references
            foreach (@$offer->segmentFlightRef as $referenceEntry) {

                // unify to array
                if (!is_array($referenceEntry)) {
                    $referenceEntry = [$referenceEntry];
                }

                foreach ($referenceEntry as $referenceCollection) {

                    // in case the reference entry is already a leg collection
                    // break the loop and setup the response legs
                    if (
                        isset($referenceCollection->refQualifier)
                        && $referenceCollection->refQualifier === self::SEGMENT_REF_QUALIFIER
                    )
                    {
                        $requestedLegs = $this->setupResponseLegs($referenceEntry);
                        break;
                    }

                    $referenceDetails = $referenceCollection->referencingDetail;
                    if (!is_array($referenceDetails)) {
                        $referenceDetails = [$referenceDetails];
                    }

                    $done = false;

                    // iterate through the default reference details to identify segment references
                    foreach ($referenceDetails as $singleSegmentReferenceDetail) {

                        // in case the first entry is a segment setup the request legs and close the looping
                        if (
                            $singleSegmentReferenceDetail->refQualifier === self::SEGMENT_REF_QUALIFIER
                        )
                        {
                            $requestedLegs = $this->setupResponseLegs($referenceDetails);
                            $done = true;
                            break;
                        }
                    }

                    if ($done === true) {
                        break;
                    }
                }
            }

            $result
                ->setItinerary(new SearchResponse\ItineraryResult())
                ->getItinerary()
                    ->setLegs($requestedLegs);

            $paxFareProduct = @$offer->paxFareProduct;
            if (!is_array($paxFareProduct)) {
                $paxFareProduct = [$paxFareProduct];
            }

            $fareProducts = new ArrayCollection($paxFareProduct);
            $currency = @$this->amadeusResult->response->conversionRate->conversionRateDetail[0]->currency;

            $result->setCalculation(new SearchResponse\CalculationResult());

            if ($currency !== null) {
                $result->getCalculation()->setCurrency($currency);
            }

            // setup calculation & fare
            $result
                ->getCalculation()
                ->setFlight(new SearchResponse\Flight())
                ->getFlight()
                ->setFare(new SearchResponse\PriceBreakdown())
                ->getFare()
                ->setPassengerTypes(new SearchResponse\PassengerTypes());

            // setup tax
            $result
                ->getCalculation()
                ->getFlight()
                ->setTax(new SearchResponse\PriceBreakdown())
                ->getTax()
                ->setPassengerTypes(new SearchResponse\PassengerTypes());

            // filter for adult fare
            $adultFares = $fareProducts->filter(
                function ($fareProduct) {
                    $preference = @$fareProduct->paxReference;
                    return $preference->ptc === self::PTC_ADULT;
                }
            );

            // setup pricing information based on adult fare
            $adultFare = $adultFares->first();
            if ($adultFare) {

                $totalAmount = @$adultFare->paxFareDetail->totalFareAmount;
                if ($totalAmount !== null) {
                    $result
                        ->getCalculation()
                        ->getFlight()
                        ->getFare()
                        ->getPassengerTypes()
                        ->setAdult($totalAmount);

                    $adultCount = count(@$adultFare->paxReference->traveller);
                    if ($adultCount !== null) {
                        $currentTotal = $result->getCalculation()->getFlight()->getFare()->getTotal();
                        $result
                            ->getCalculation()
                            ->getFlight()
                            ->getFare()
                            ->setTotal($currentTotal + ($adultCount * $totalAmount));
                    }
                }

                $totalTax = @$adultFare->paxFareDetail->totalTaxAmount;
                if ($totalTax !== null) {
                    $result
                        ->getCalculation()
                        ->getFlight()
                        ->getTax()
                        ->getPassengerTypes()
                        ->setAdult($totalTax);

                    $adultCount = count(@$adultFare->paxReference->traveller);
                    if ($adultCount !== null) {
                        $currentTotal = $result->getCalculation()->getFlight()->getTax()->getTotal();
                        $result
                            ->getCalculation()
                            ->getFlight()
                            ->getTax()
                            ->setTotal($currentTotal + ($adultCount * $totalTax));
                    }
                }
            }

            // filter for child fare
            $childFares = $fareProducts->filter(
                function ($fareProduct) {
                    $preference = @$fareProduct->paxReference;
                    return $preference->ptc === self::PTC_CHILD;
                }
            );

            // setup pricing information based on child fare
            $childFare = $childFares->first();
            if ($childFare) {

                $totalAmount = @$childFare->paxFareDetail->totalFareAmount;
                if ($totalAmount !== null) {
                    $result
                        ->getCalculation()
                        ->getFlight()
                        ->getFare()
                        ->getPassengerTypes()
                        ->setChild($totalAmount);

                    $childCount = count(@$childFare->paxReference->traveller);
                    if ($childCount !== null) {
                        $currentTotal = $result->getCalculation()->getFlight()->getFare()->getTotal();
                        $result
                            ->getCalculation()
                            ->getFlight()
                            ->getFare()
                            ->setTotal($currentTotal + ($childCount * $totalAmount));
                    }
                }

                $totalTax = @$childFare->paxFareDetail->totalTaxAmount;
                if ($totalTax !== null) {
                    $result
                        ->getCalculation()
                        ->getFlight()
                        ->getTax()
                        ->getPassengerTypes()
                        ->setChild($totalTax);

                    $childCount = count(@$childFare->paxReference->traveller);
                    if ($childCount !== null) {
                        $currentTotal = $result->getCalculation()->getFlight()->getTax()->getTotal();
                        $result
                            ->getCalculation()
                            ->getFlight()
                            ->getTax()
                            ->setTotal($currentTotal + ($childCount * $totalTax));
                    }
                }
            }

            $searchResponse->getResult()->add($result);
        }

        $this->mappedResponse = $searchResponse;
    }

    /**
     * Generate a leg collection for an search response based of
     * reference entries off a recommendation
     * @param array $segmentReferences
     * @return ArrayCollection
     */
    private function setupResponseLegs($segmentReferences)
    {
        $requestedLegs = new ArrayCollection();

        foreach ($segmentReferences as $index => $legReference) {

            if (!$requestedLegs->offsetExists($index)) {
                $requestedLegs->set($index, new ArrayCollection());
            }

            $requestedLegs
                ->get($index)
                ->add(
                    $this->legCollection
                        ->get($index)
                            ->get(--$legReference->refNumber)
                );
        }

        return $requestedLegs;
    }

    /**
     * Method to setup the leg collection for an itinerary
     * @param int $offset
     * @return ArrayCollection
     */
    private function mapLegs()
    {
        $legCollection = new ArrayCollection();
        $flightIndex = @$this->amadeusResult->response->flightIndex;

        if (!is_array($flightIndex)) {
            $flightIndex = [$flightIndex];
        }

        foreach ($flightIndex as $itineraryDirection) {
            $indexLegCollection = new ArrayCollection();
            $groupOfFlights = @$itineraryDirection->groupOfFlights;

            if (!is_array($groupOfFlights)) {
                $groupOfFlights = [$groupOfFlights];
            }
            $groupOfFlights = new \ArrayObject($groupOfFlights);

            foreach ($groupOfFlights as $leg) {
                $itineraryLeg = new SearchResponse\Leg();
                $itineraryLeg->setSegments(new ArrayCollection());

                $segments = @$leg->flightDetails;
                if (!is_array($segments)) {
                    $segments = [$segments];
                }

                $proposals = @$leg->propFlightGrDetail->flightProposal;
                if (!is_array($proposals)) {
                    $proposals = [$proposals];
                }

                $proposalCollection = new ArrayCollection($proposals);
                $estimatedFlightTime = $proposalCollection->filter(
                    function ($element) {
                        return @$element->unitQualifier === 'EFT';
                    }
                )->first();

                $mainCarrier = $proposalCollection->filter(
                    function ($element) {
                        return @$element->unitQualifier === 'MCX';
                    }
                )->first();

                if ($estimatedFlightTime && $estimatedFlightTime->ref !== null) {
                    $hours = (integer) substr($estimatedFlightTime->ref, 0, 2);
                    $minutes = (integer) substr($estimatedFlightTime->ref, 2, 2);
                    $itineraryLeg->setDuration($hours * 60 * 60 + $minutes * 60);
                }

                if ($mainCarrier && $mainCarrier->ref !== null) {
                    $itineraryLeg
                        ->setCarriers(new SearchResponse\Carriers())
                        ->getCarriers()
                            ->setMain(new SearchResponse\Carrier())
                            ->getMain()
                                ->setIata($mainCarrier->ref);
                }

                foreach ($segments as $segment) {
                    $legSegment = new SearchResponse\Segment();

                    // set arrival and departure
                    $legSegment->setAirports(new SearchResponse\Airports());

                    $departure = @$segment->flightInformation->location[0]->locationId;
                    $arrival = @$segment->flightInformation->location[1]->locationId;

                    if ($departure !== null) {
                        $legSegment
                            ->getAirports()
                                ->setDeparture(new SearchResponse\Location())
                                ->getDeparture()
                                    ->setIata($departure);
                    }

                    if ($arrival !== null) {
                        $legSegment
                            ->getAirports()
                                ->setArrival(new SearchResponse\Location())
                                ->getArrival()
                                    ->setIata($arrival);
                    }

                    // set arrive-at and depart-at
                    $departAtDate = @$segment->flightInformation->productDateTime->dateOfDeparture;
                    $departAtTime = @$segment->flightInformation->productDateTime->timeOfDeparture;

                    $arriveAtDate = @$segment->flightInformation->productDateTime->dateOfArrival;
                    $arriveAtTime = @$segment->flightInformation->productDateTime->timeOfArrival;

                    if ($departAtDate !== null && $departAtTime !== null) {
                        $legSegment->setDepartAt(
                            \DateTime::createFromFormat('dmyHi', "$departAtDate$departAtTime")
                        );
                    }

                    if ($arriveAtDate !== null && $arriveAtTime !== null) {
                        $legSegment->setArriveAt(
                            \DateTime::createFromFormat('dmyHi', "$arriveAtDate$arriveAtTime")
                        );
                    }

                    // set carriers
                    $marketingCarrier = @$segment->flightInformation->companyId->marketingCarrier;
                    $operatingCarrier = @$segment->flightInformation->companyId->operatingCarrier;

                    $legSegment->setCarriers(new SearchResponse\Carriers());

                    if ($marketingCarrier !== null) {
                        $legSegment
                            ->getCarriers()
                                ->setMarketing(new SearchResponse\Carrier())
                                ->getMarketing()
                                    ->setIata($marketingCarrier);
                    }

                    if ($operatingCarrier !== null) {
                        $legSegment
                            ->getCarriers()
                                ->setOperating(new SearchResponse\Carrier())
                                ->getOperating()
                                    ->setIata($operatingCarrier);
                    }

                    // set flight number
                    $flightNumber = @$segment->flightInformation->flightNumber;

                    if ($flightNumber !== null) {
                        $legSegment
                            ->setFlightNumber($flightNumber);
                    }

                    // set aircraft type
                    $aircraft = @$segment->flightInformation->productDetail->equipmentType;

                    if ($aircraft !== null) {
                        $legSegment
                            ->setAircraftType($aircraft);
                    }

                    $itineraryLeg->getSegments()->add($legSegment);
                }

                $indexLegCollection->add($itineraryLeg);
            }

            $legCollection->add($indexLegCollection);
        }

        return $legCollection;
    }

    /**
     * @return SearchResponse
     */
    public function getMappedResponse()
    {
        return $this->mappedResponse;
    }

    /**
     * @return string
     */
    public function getMappedResponseAsJson()
    {
        return $this->mapper->createJson($this->getMappedResponse());
    }
}