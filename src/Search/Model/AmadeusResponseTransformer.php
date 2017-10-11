<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;

/**
 * Class AmadeusResponseTransformer
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseTransformer
{
    private const PTC_CHILD = 'CH';

    private const PTC_ADULT = 'ADT';

    private const CLASSIFICATION_SCHEDULED = 'scheduled';

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
     * @param Mapper $mapper
     *
     * AmadeusResponseTransformer constructor.
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function mapResultToDefinedStructure(Result $amadeusResult) : SearchResponse
    {
        $this->amadeusResult = $amadeusResult;

        /** @var SearchResponse $searchResponse */
        $searchResponse = new SearchResponse();
        $searchResponse->setResult(new ArrayCollection());

        // map the flight index to a leg collection the first layer represents the requested leg
        // the second layer the indexed legs for said requested leg
        $legCollection = $this->mapLegs();

        // A single <recommendation> can contain multiple flights.
        // Returns a flat list of all flights from the recommendations and their segmentFlightRefs
        $allOffers = function () {
            $recommendations = new NodeList($this->amadeusResult->response->recommendation);

            foreach ($recommendations as $recommendation) {
                $segmentFlightRefs = SegmentFlightRefs::fromRecommendation($recommendation);

                foreach ($segmentFlightRefs->getSegmentRefsForFlights() as $segmentFlightRef) {
                    yield [$recommendation, $segmentFlightRef];
                }
            }
        };

        // iterate through the recommendations
        foreach ($allOffers() as list($recommendation, $segmentFlightRefs)) {
            $result = new SearchResponse\Result();

            $result
                ->setItinerary(new SearchResponse\ItineraryResult())
                ->getItinerary()
                    ->setLegs($this->setupResponseLegs($legCollection, $segmentFlightRefs));

            $fareProducts = new NodeList($recommendation->paxFareProduct);

            $result->setCalculation(new SearchResponse\CalculationResult());

            $conversionRateDetail = new NodeList($this->amadeusResult->response->conversionRate->conversionRateDetail);

            if (!$conversionRateDetail->isEmpty()) {
                $result->getCalculation()->setCurrency($conversionRateDetail->first()->currency);
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

            $result->getCalculation()->getFlight()->setTotal(
                $result->getCalculation()->getFlight()->getFare()->getTotal()
                + $result->getCalculation()->getFlight()->getTax()->getTotal()
            );

            $fareDetails = new NodeList($fareProducts->first()->fareDetails);

            foreach ($fareDetails as $legIndex => $singleFareDetail) {
                $groupOfFares = new NodeList($singleFareDetail->groupOfFares);

                foreach ($groupOfFares as $segmentIndex => $segmentFare) {

                    // add cabin class
                    /** @var SearchResponse\Segment $legSegment */
                    $legSegment = $result
                        ->getItinerary()
                            ->getLegs()
                                ->offsetGet($legIndex)
                                ->first()
                                    ->getSegments()
                                        ->offsetGet($segmentIndex);

                    $legSegment
                        ->setCabinClass(new SearchResponse\CabinClass())
                        ->getCabinClass()
                            ->setCode(@$segmentFare->productInformation->cabinProduct->cabin);

                    // add remaining seats
                    $legSegment->setRemainingSeats($segmentFare->productInformation->cabinProduct->avlStatus);
                }
            }

            $searchResponse->getResult()->add($result);
        }

        return $searchResponse;
    }

    /**
     * Generate a leg collection for an search response based of
     * reference entries off a recommendation
     *
     * @param Collection       $legCollection
     * @param SegmentFlightRef $segmentFlightRefs
     * @return ArrayCollection
     */
    private function setupResponseLegs(Collection $legCollection, SegmentFlightRef $segmentFlightRefs): ArrayCollection
    {
        $requestedLegs = new ArrayCollection();

        foreach ($segmentFlightRefs->getSegmentRefNumbers() as $legIndex => $flightRefNumber) {
            $requestedLegs->set($legIndex, new ArrayCollection());

            $requestedLegs
                ->get($legIndex)
                ->add(
                    $legCollection
                        ->get($legIndex)
                        ->get($flightRefNumber - 1)
                );
        }

        return $requestedLegs;
    }

    /**
     * Method to setup the leg collection for an itinerary
     *
     * @return Collection
     */
    private function mapLegs() : Collection
    {
        $legCollection = new ArrayCollection();
        $flightIndex = new NodeList($this->amadeusResult->response->flightIndex);

        foreach ($flightIndex as $itineraryDirection) {
            $indexLegCollection = new ArrayCollection();
            $groupOfFlights = new NodeList($itineraryDirection->groupOfFlights);

            foreach ($groupOfFlights as $leg) {
                $itineraryLeg = new SearchResponse\Leg();
                $itineraryLeg->setClassification(self::CLASSIFICATION_SCHEDULED);
                $itineraryLeg->setSegments(new ArrayCollection());

                $segments = new NodeList($leg->flightDetails);

                $proposals = FlightProposals::fromGroupOfFlights($leg);

                if ($proposals->hasElapsedFlyingTime()) {
                    $itineraryLeg->setDuration($proposals->getElapsedFlyingTime());
                }

                if ($proposals->hasMajorityCarrier()) {
                    $itineraryLeg
                        ->setCarriers(new SearchResponse\Carriers())
                        ->getCarriers()
                            ->setMain(new SearchResponse\Carrier())
                            ->getMain()
                                ->setIata($proposals->getMajorityCarrier());
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

                $itineraryLeg->setNights((new Nights($itineraryLeg->getSegments()))->getNights());

                $indexLegCollection->add($itineraryLeg);
            }

            $legCollection->add($indexLegCollection);
        }

        return $legCollection;
    }

    /**
     * @param SearchResponse $mappedResponse
     * @return string
     */
    public function getMappedResponseAsJson(SearchResponse $mappedResponse)
    {
        return $this->mapper->createJson($mappedResponse);
    }
}
