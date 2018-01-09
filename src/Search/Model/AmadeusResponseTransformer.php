<?php
namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Request as SearchRequest;

/**
 * Class AmadeusResponseTransformer
 * @package Flight\Service\Amadeus\Search\Model
 */
class AmadeusResponseTransformer
{
    private const CLASSIFICATION_SCHEDULED = 'scheduled';

    /**
     * Build a response with an empty result
     *
     * @return SearchResponse
     */
    public function createEmptyResponse() : SearchResponse
    {
        return (new SearchResponse())->setResult(new ArrayCollection());
    }

    /**
     * @param BusinessCase  $businessCase
     * @param SearchRequest $request
     * @param Result        $amadeusResult
     *
     * @return SearchResponse
     */
    public function mapResultToDefinedStructure(BusinessCase $businessCase, SearchRequest $request, Result $amadeusResult) : SearchResponse
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

            $this->setupItinerary(
                $result,
                $businessCase,
                $segmentFlightRefs,
                $legIndex,
                $freeBaggageIndex,
                $fareProducts
            );

            $this->setupCalculation(
                $result,
                $request,
                $conversionRateDetail,
                $fareProducts
            );

            $searchResponse->getResult()->add($result);
        }

        return $searchResponse;
    }

    /**
     * @param SearchResponse\Result $result
     * @param BusinessCase          $businessCase
     * @param SegmentFlightref      $segmentFlightRefs
     * @param LegIndex              $legIndex
     * @param FreeBaggageIndex      $freeBaggageIndex
     * @param Collection            $fareProducts
     */
    private function setupItinerary(
        SearchResponse\Result $result,
        BusinessCase $businessCase,
        SegmentFlightref $segmentFlightRefs,
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        Collection $fareProducts
    ) : void {

        $result
            ->setItinerary(new SearchResponse\ItineraryResult())
            ->getItinerary()
            ->setType($businessCase->getType())
            ->setLegs(new ArrayCollection());

        $fareDetails = new NodeList($fareProducts->first()->fareDetails);
        $validatingCarrier = new ValidatingCarrier($fareProducts);

        foreach ($segmentFlightRefs->getSegmentRefNumbers() as $legOffset => $refToGroupOfFlights) {
            $leg = $this->mapLeg(
                $legIndex,
                $freeBaggageIndex,
                $legOffset,
                $refToGroupOfFlights,
                $fareDetails,
                $validatingCarrier
            );

            $result->getItinerary()->getLegs()->add(new ArrayCollection([$leg]));
        }
    }

    /**
     * @param SearchResponse\Result $result
     * @param SearchRequest         $request
     * @param Collection            $conversionRateDetail
     * @param Collection            $fareProducts
     */
    private function setupCalculation(
        SearchResponse\Result $result,
        SearchRequest $request,
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

        $adultFares = PaxFareDetails::adultFromFareProducts($fareProducts);
        $childFares = PaxFareDetails::childFromFareProducts($fareProducts);
        $infantFares = PaxFareDetails::infantFromFareProducts($fareProducts);

        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setAdult($adultFares->getFarePerPax() );
        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setChild($request->getChildren() ? $childFares->getFarePerPax() : null);
        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setInfant($request->getInfants() ? $infantFares->getFarePerPax() : null);
        $result->getCalculation()->getFlight()->getFare()->setTotal(
            $adultFares->getTotalFareAmount() + $childFares->getTotalFareAmount() + $infantFares->getTotalFareAmount()
        );

        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setAdult($adultFares->getTaxPerPax());
        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setChild($request->getChildren() ? $childFares->getTaxPerPax() : null);
        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setInfant($request->getInfants() ? $infantFares->getTaxPerPax(): null);
        $result->getCalculation()->getFlight()->getTax()->setTotal(
            $adultFares->getTotalTaxAmount() + $childFares->getTotalTaxAmount() + $infantFares->getTotalTaxAmount()
        );

        $result->getCalculation()->getFlight()->setTotal(
            $result->getCalculation()->getFlight()->getFare()->getTotal()
            + $result->getCalculation()->getFlight()->getTax()->getTotal()
        );

        $defaultPaymentMethod = new SearchResponse\PaymentMethod();
        $defaultPaymentMethod->setPaymentFee(new SearchResponse\PriceBreakdown());
        $defaultPaymentMethod->getPaymentFee()->setPassengerTypes(new SearchResponse\PassengerTypes());
        $defaultPaymentMethod->setName('_default');

        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setAdult($adultFares->getPaymentFeesPerPax());
        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setChild($request->getChildren() ? $childFares->getPaymentFeesPerPax() : null);
        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setInfant($request->getInfants() ? $infantFares->getPaymentFeesPerPax() : null);
        $defaultPaymentMethod->getPaymentFee()->setTotal(
            $adultFares->getTotalPaymentFees() + $childFares->getTotalPaymentFees() + $infantFares->getTotalPaymentFees()
        );

        $result->getCalculation()->setPaymentMethods(new ArrayCollection([$defaultPaymentMethod]));
    }

    /**
     * Convert the leg
     *
     * @param LegIndex          $legIndex
     * @param FreeBaggageIndex  $freeBaggageIndex
     * @param string            $legOffset
     * @param string            $refToGroupOfFlights
     * @param Collection        $fareDetails
     * @param ValidatingCarrier $validatingCarrier
     * @return SearchResponse\Leg
     */
    private function mapLeg(
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        string $legOffset,
        string $refToGroupOfFlights,
        Collection $fareDetails,
        ValidatingCarrier $validatingCarrier
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

        $itineraryLeg->setCarriers(new SearchResponse\Carriers());

        if ($proposals->hasMajorityCarrier()) {
            $itineraryLeg->getCarriers()
                ->setMain(new SearchResponse\Carrier())
                ->getMain()
                    ->setIata($proposals->getMajorityCarrier());
        }

        $validatingCarrier->addToCarriers($itineraryLeg->getCarriers());

        foreach ($segments as $segmentOffset => $segment) {
            $legSegment = new SearchResponse\Segment();

            // set arrival and departure
            $legSegment->setAirports(new SearchResponse\Airports());

            $departure = $segment->flightInformation->location[0]->locationId ?? null;
            $arrival = $segment->flightInformation->location[1]->locationId ?? null;

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
            $departAtDate = $segment->flightInformation->productDateTime->dateOfDeparture ?? null;
            $departAtTime = $segment->flightInformation->productDateTime->timeOfDeparture ?? null;

            $arriveAtDate = $segment->flightInformation->productDateTime->dateOfArrival ?? null;
            $arriveAtTime = $segment->flightInformation->productDateTime->timeOfArrival ?? null;

            if ($departAtDate !== null && $departAtTime !== null) {
                $legSegment->setDepartAt(
                    DateTime::fromDateAndTime($departAtDate, $departAtTime)
                );
            }

            if ($arriveAtDate !== null && $arriveAtTime !== null) {
                $legSegment->setArriveAt(
                    DateTime::fromDateAndTime($arriveAtDate, $arriveAtTime)
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
            $aircraft = $segment->flightInformation->productDetail->equipmentType ?? null;

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

            TechnicalStops::writeToSegment($legSegment, $segment);

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
}
