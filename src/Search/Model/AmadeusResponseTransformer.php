<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;

/**
 * Class AmadeusResponseTransformer
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseTransformer
{
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
        $legCollection = $this->mapLegs();

        // iterate through the recommendations
        foreach ($offers as $offer) {
            $result = new SearchResponse\Result();
            $requestedLegs = new ArrayCollection();

            // iterate through the flight references
            foreach (@$offer->segmentFlightRef as $referenceEntry) { // offer references
                if (!is_array($referenceEntry)) {
                    $referenceEntry = [$referenceEntry];
                }

                foreach ($referenceEntry as $referenceCollection) {

                    $referenceDetails = $referenceCollection->referencingDetail;
                    if (!is_array($referenceDetails)) {
                        $referenceDetails = [$referenceDetail];
                    }

                    $skip = false;

                    // this loop will iterate through the referencing details of one reference
                    // one detail can be a segment (LEG) -- since the flight index is structured
                    // the way that the first layer is the leg index, means that it is necessary to
                    // keep track of the count so the legs are requested from the correct collection
                    foreach ($referenceDetails as $referenceIndex => $singleSegmentReferenceDetail) {

                        // if the first detail is no segment break the loop
                        if ($singleSegmentReferenceDetail->refQualifier !== 'S') {
                            $skip = true;
                            break;
                        }

                        // if there are no legs registered for a leg index add a collection
                        if (!$requestedLegs->offsetExists($referenceIndex)) {
                            $requestedLegs->set($referenceIndex, new ArrayCollection());
                        }

                        $indexRefNumber = --$singleSegmentReferenceDetail->refNumber;

                        $requestedLegs
                            ->get($referenceIndex)
                                ->add(
                                    $legCollection
                                        ->get($referenceIndex)
                                            ->get($indexRefNumber)
                                );
                    }

                    if (!$skip) {
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
                    return $preference->ptc === 'ADT';
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
                    return $preference->ptc === 'CH';
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