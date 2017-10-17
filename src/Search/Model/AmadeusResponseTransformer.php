<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;
use Flight\SearchRequestMapping\Entity\BusinessCase;

/**
 * Class AmadeusResponseTransformer
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseTransformer
{
    private const PTC_CHILD = 'CH';
    private const PTC_ADULT = 'ADT';
    private const PTC_INFANT = 'INF';

    private const CLASSIFICATION_SCHEDULED = 'scheduled';

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @param Mapper $mapper
     *
     * AmadeusResponseTransformer constructor.
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param BusinessCase $businessCase
     * @param Result       $amadeusResult
     * @return SearchResponse
     */
    public function mapResultToDefinedStructure(BusinessCase $businessCase, Result $amadeusResult) : SearchResponse
    {
        $searchResponse = new SearchResponse();
        $searchResponse->setResult(new ArrayCollection());

        $legIndex = new LegIndex($amadeusResult);
        $freeBaggageIndex = new FreeBaggageIndex($amadeusResult);
        $conversionRateDetail = new NodeList($amadeusResult->response->conversionRate->conversionRateDetail);


        // A single <recommendation> can contain multiple flights.
        // Returns a flat list of all flights from the recommendations and their segmentFlightRefs
        $allOffers = function (Result $amadeusResult) {
            foreach (new NodeList($amadeusResult->response->recommendation) as $recommendation) {
                $segmentFlightRefs = SegmentFlightRefs::fromRecommendation($recommendation);

                foreach ($segmentFlightRefs->getSegmentRefsForFlights() as $segmentFlightRef) {
                    yield [$recommendation, $segmentFlightRef];
                }
            }
        };

        // iterate through the recommendations
        foreach ($allOffers($amadeusResult) as list($recommendation, $segmentFlightRefs)) {
            $result = new SearchResponse\Result();

            $fareProducts = new NodeList($recommendation->paxFareProduct);
            $fareDetails = new NodeList($fareProducts->first()->fareDetails);

            $this->setupItinerary(
                $result,
                $businessCase,
                $segmentFlightRefs,
                $legIndex,
                $freeBaggageIndex,
                $fareDetails
            );

            $this->setupCalculation(
                $result,
                $conversionRateDetail,
                $fareProducts
            );

            $searchResponse->getResult()->add($result);

            if ($businessCase->getOptions()->getResultLimit()
                && $searchResponse->getResult()->count() >= $businessCase->getOptions()->getResultLimit()
            ) {
                break;
            }
        }

        return $searchResponse;
    }

    /**
     * @param SearchResponse\Result $result
     * @param BusinessCase          $businessCase
     * @param SegmentFlightref      $segmentFlightRefs
     * @param LegIndex              $legIndex
     * @param FreeBaggageIndex      $freeBaggageIndex
     * @param Collection            $fareDetails
     */
    private function setupItinerary(
        SearchResponse\Result $result,
        BusinessCase $businessCase,
        SegmentFlightref $segmentFlightRefs,
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        Collection $fareDetails
    ) : void {

        $result
            ->setItinerary(new SearchResponse\ItineraryResult())
            ->getItinerary()
            ->setType($businessCase->getType())
            ->setLegs(new ArrayCollection());


        foreach ($segmentFlightRefs->getSegmentRefNumbers() as $legOffset => $refToGroupOfFlights) {
            $leg = $this->mapLeg(
                $legIndex,
                $freeBaggageIndex,
                $legOffset,
                $refToGroupOfFlights,
                $fareDetails
            );

            $result->getItinerary()->getLegs()->add(new ArrayCollection([$leg]));
        }
    }

    /**
     * @param SearchResponse\Result $result
     * @param Collection            $conversionRateDetail
     * @param Collection            $fareProducts
     */
    private function setupCalculation(
        SearchResponse\Result $result,
        Collection $conversionRateDetail,
        Collection $fareProducts
    ) : void {

        $result->setCalculation(new SearchResponse\CalculationResult());

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

        $adultFares = $fareProducts->filter(
            function ($fareProduct) {
                return $fareProduct->paxReference->ptc === self::PTC_ADULT;
            }
        )->first();
        $childFares = $fareProducts->filter(
            function ($fareProduct) {
                return $fareProduct->paxReference->ptc === self::PTC_CHILD;
            }
        )->first();
        $infantFares = $fareProducts->filter(
            function ($fareProduct) {
                return $fareProduct->paxReference->ptc === self::PTC_INFANT;
            }
        )->first();

        $fares = $result
            ->getCalculation()
            ->getFlight()
            ->getFare();

        $fares->getPassengerTypes()
            ->setAdult($adultFares->paxFareDetail->totalFareAmount ?? 0.0)
            ->setChild($childFares->paxFareDetail->totalFareAmount ?? 0.0)
            ->setInfant($infantFares->paxFareDetail->totalFareAmount ?? 0.0);

        $fares->setTotal(
            count($adultFares->paxReference->traveller ?? []) * $fares->getPassengerTypes()->getAdult()
            + count($childFares->paxReference->traveller ?? []) * $fares->getPassengerTypes()->getChild()
            + count($infantFares->paxReference->traveller ?? []) * $fares->getPassengerTypes()->getInfant()
        );

        $taxes = $result
            ->getCalculation()
            ->getFlight()
            ->getTax();

        $taxes->getPassengerTypes()
            ->setAdult($adultFares->paxFareDetail->totalTaxAmount ?? 0.0)
            ->setChild($childFares->paxFareDetail->totalTaxAmount ?? 0.0)
            ->setInfant($infantFares->paxFareDetail->totalTaxAmount ?? 0.0);

        $taxes->setTotal(
            count($adultFares->paxReference->traveller ?? []) * $taxes->getPassengerTypes()->getAdult()
            + count($childFares->paxReference->traveller ?? []) * $taxes->getPassengerTypes()->getChild()
            + count($infantFares->paxReference->traveller ?? []) * $taxes->getPassengerTypes()->getInfant()
        );

        $result->getCalculation()->getFlight()->setTotal(
            $result->getCalculation()->getFlight()->getFare()->getTotal()
            + $result->getCalculation()->getFlight()->getTax()->getTotal()
        );
    }

    /**
     * Convert the leg
     *
     * @param LegIndex         $legIndex
     * @param FreeBaggageIndex $freeBaggageIndex
     * @param string           $legOffset
     * @param string           $refToGroupOfFlights
     * @param Collection       $fareDetails
     * @return SearchResponse\Leg
     */
    private function mapLeg(
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        string $legOffset,
        string $refToGroupOfFlights,
        Collection $fareDetails
    ) : SearchResponse\Leg {

        $itineraryLeg = new SearchResponse\Leg();
        $itineraryLeg->setClassification(self::CLASSIFICATION_SCHEDULED);
        $itineraryLeg->setSegments(new ArrayCollection());

        $groupOfFlights = $legIndex->groupOfFlights($legOffset, $refToGroupOfFlights);
        $segments = new NodeList($groupOfFlights->flightDetails);

        $proposals = FlightProposals::fromGroupOfFlights($groupOfFlights);

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

        foreach ($segments as $segmentOffset => $segment) {
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
            $marketingCarrier = $segment->flightInformation->companyId->marketingCarrier ?? null;
            $operatingCarrier = $segment->flightInformation->companyId->operatingCarrier ?? null;

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
            $flightNumber = $segment->flightInformation->flightNumber
                ?? $segment->flightInformation->flightOrtrainNumber
                ?? null;

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

            $baggageDetails = $freeBaggageIndex->getFreeBagAllowanceInfo($refToGroupOfFlights, $legOffset + 1, $segmentOffset + 1);

            if ($baggageDetails) {
                if ($baggageDetails->quantityCode === 'W') {
                    $legSegment->setBaggageRules(new SearchResponse\BaggageRules());
                    $legSegment->getBaggageRules()
                        ->setWeight($baggageDetails->freeAllowance)
                        ->setUnit('kg');
                } elseif ($baggageDetails->quantityCode === 'N') {
                    $legSegment->setBaggageRules(new SearchResponse\BaggageRules());
                    $legSegment->getBaggageRules()
                        ->setPieces($baggageDetails->freeAllowance);
                }
            }

            $itineraryLeg->getSegments()->add($legSegment);
        }

        $itineraryLeg->setNights(Nights::calc($itineraryLeg->getSegments()));

        $groupOfFares = new NodeList($fareDetails->get($legOffset)->groupOfFares);

        foreach ($groupOfFares as $segmentIndex => $segmentFare) {
            // add cabin class
            /** @var SearchResponse\Segment $legSegment */
            $legSegment = $itineraryLeg
                ->getSegments()
                ->offsetGet($segmentIndex);

            if (CabinClass::code($segmentFare)) {
                $legSegment
                    ->setCabinClass(new SearchResponse\CabinClass())
                    ->getCabinClass()
                    ->setCode(CabinClass::code($segmentFare))
                    ->setName(CabinClass::name($segmentFare));
            }

            $legSegment->setGdsInformation(new SearchResponse\AmadeusSegmentGdsInformation());
            $legSegment->getGdsInformation()->setResBookDesigCode(CabinClass::rbd($segmentFare));

            // add remaining seats
            $legSegment->setRemainingSeats($segmentFare->productInformation->cabinProduct->avlStatus);
        }

        return $itineraryLeg;
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
