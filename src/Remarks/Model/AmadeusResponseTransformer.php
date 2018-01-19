<?php
namespace Flight\Service\Amadeus\Remarks\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;

/**
 * Class AmadeusResponseTransformer
 * @package Flight\Service\Amadeus\Remarks\Model
 */
class AmadeusResponseTransformer
{
    private const CLASSIFICATION_SCHEDULED = 'scheduled';

    public function mapResultRemarksRead(Result $result)
    {
        $remarksResponse = new ResultResponse();
        $remarksResponse->setResult(new ArrayCollection());
        $remarksCollection = new ArrayCollection();
        foreach ($result->response->dataElementsMaster->dataElementsIndiv as $remarks) {
            $remarksData = $remarks->elementManagementData;
            if (!isset($remarks->miscellaneousRemarks)) {
                continue;
            }
            $remarksDataAdd = $remarks->miscellaneousRemarks;

            $remarksCollection->add((new Remark())->setType($remarksDataAdd->remarks->type)->convertFromCrs($remarksDataAdd->remarks->freetext)
                ->setManagementData(
                    (new ManagementData())->setLineNumber($remarksData->lineNumber)->setReference(
                        (new Reference())->setNumber($remarksData->reference->number)->setQualifier($remarksData->reference->qualifier)
                    )->setSegmentName($remarksData->segmentName)
                ));
        }

        $itinerary = new Itinerary();
        $itinerary->setRemarks($remarksCollection);
        $remarksResponse->getResult()->add($itinerary);

        return $remarksResponse;
    }

    public function mapResultRemarksAdd(Result $result)
    {
        return '';
    }

    public function mapResultRemarksDelete(Result $result)
    {
        return '';
    }

    /**
     * @param BusinessCase $businessCase
     * @param Result       $amadeusResult
     * @return RemarksResponse
     */
    public function mapResultToDefinedStructure(BusinessCase $businessCase, Result $amadeusResult) : RemarksResponse
    {
        $remarksResponse = new ResultResponse();
        $remarksResponse->setResult(new ArrayCollection());

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
            $result = new RemarksResponse\Result();

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
                $conversionRateDetail,
                $fareProducts
            );

            $remarksResponse->getResult()->add($result);
        }

        return $remarksResponse;
    }

    /**
     * @param RemarksResponse\Result $result
     * @param BusinessCase          $businessCase
     * @param SegmentFlightref      $segmentFlightRefs
     * @param LegIndex              $legIndex
     * @param FreeBaggageIndex      $freeBaggageIndex
     * @param Collection            $fareProducts
     */
    private function setupItinerary(
        RemarksResponse\Result $result,
        BusinessCase $businessCase,
        SegmentFlightref $segmentFlightRefs,
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        Collection $fareProducts
    ) : void {

        $result
            ->setItinerary(new RemarksResponse\ItineraryResult())
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
     * @param RemarksResponse\Result $result
     * @param Collection            $conversionRateDetail
     * @param Collection            $fareProducts
     */
    private function setupCalculation(
        RemarksResponse\Result $result,
        Collection $conversionRateDetail,
        Collection $fareProducts
    ) : void {

        $result->setCalculation(new RemarksResponse\CalculationResult());

        if (!$conversionRateDetail->isEmpty()) {
            $result->getCalculation()->setCurrency($conversionRateDetail->first()->currency);
        }

        // setup calculation & fare
        $result
            ->getCalculation()
            ->setFlight(new RemarksResponse\Flight())
            ->getFlight()
            ->setFare(new RemarksResponse\PriceBreakdown())
            ->getFare()
            ->setPassengerTypes(new RemarksResponse\PassengerTypes());

        // setup tax
        $result
            ->getCalculation()
            ->getFlight()
            ->setTax(new RemarksResponse\PriceBreakdown())
            ->getTax()
            ->setPassengerTypes(new RemarksResponse\PassengerTypes());

        $adultFares = PaxFareDetails::adultFromFareProducts($fareProducts);
        $childFares = PaxFareDetails::childFromFareProducts($fareProducts);
        $infantFares = PaxFareDetails::infantFromFareProducts($fareProducts);

        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setAdult($adultFares->getFarePerPax());
        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setChild($childFares->getFarePerPax());
        $result->getCalculation()->getFlight()->getFare()->getPassengerTypes()->setInfant($infantFares->getFarePerPax());
        $result->getCalculation()->getFlight()->getFare()->setTotal(
            $adultFares->getTotalFareAmount() + $childFares->getTotalFareAmount() + $infantFares->getTotalFareAmount()
        );

        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setChild($childFares->getTaxPerPax());
        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setAdult($adultFares->getTaxPerPax());
        $result->getCalculation()->getFlight()->getTax()->getPassengerTypes()->setInfant($infantFares->getTaxPerPax());
        $result->getCalculation()->getFlight()->getTax()->setTotal(
            $adultFares->getTotalTaxAmount() + $childFares->getTotalTaxAmount() + $infantFares->getTotalTaxAmount()
        );

        $result->getCalculation()->getFlight()->setTotal(
            $result->getCalculation()->getFlight()->getFare()->getTotal()
            + $result->getCalculation()->getFlight()->getTax()->getTotal()
        );

        $defaultPaymentMethod = new RemarksResponse\PaymentMethod();
        $defaultPaymentMethod->setPaymentFee(new RemarksResponse\PriceBreakdown());
        $defaultPaymentMethod->getPaymentFee()->setPassengerTypes(new RemarksResponse\PassengerTypes());
        $defaultPaymentMethod->setName('_default');

        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setAdult($adultFares->getPaymentFeesPerPax());
        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setChild($childFares->getPaymentFeesPerPax());
        $defaultPaymentMethod->getPaymentFee()->getPassengerTypes()->setInfant($infantFares->getPaymentFeesPerPax());
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
     * @return RemarksResponse\Leg
     */
    private function mapLeg(
        LegIndex $legIndex,
        FreeBaggageIndex $freeBaggageIndex,
        string $legOffset,
        string $refToGroupOfFlights,
        Collection $fareDetails,
        ValidatingCarrier $validatingCarrier
    ) : RemarksResponse\Leg {

        $itineraryLeg = new RemarksResponse\Leg();
        $itineraryLeg->setClassification(self::CLASSIFICATION_SCHEDULED);
        $itineraryLeg->setSegments(new ArrayCollection());

        $groupOfFlights = $legIndex->groupOfFlights($legOffset, $refToGroupOfFlights);
        $segments = new NodeList($groupOfFlights->flightDetails);

        $proposals = FlightProposals::fromGroupOfFlights($groupOfFlights);

        if ($proposals->hasElapsedFlyingTime()) {
            $itineraryLeg->setDuration($proposals->getElapsedFlyingTime());
        }

        $itineraryLeg->setCarriers(new RemarksResponse\Carriers());

        if ($proposals->hasMajorityCarrier()) {
            $itineraryLeg->getCarriers()
                ->setMain(new RemarksResponse\Carrier())
                ->getMain()
                    ->setIata($proposals->getMajorityCarrier());
        }

        $validatingCarrier->addToCarriers($itineraryLeg->getCarriers());

        foreach ($segments as $segmentOffset => $segment) {
            $legSegment = new RemarksResponse\Segment();

            // set arrival and departure
            $legSegment->setAirports(new RemarksResponse\Airports());

            $departure = @$segment->flightInformation->location[0]->locationId;
            $arrival = @$segment->flightInformation->location[1]->locationId;

            if ($departure !== null) {
                $legSegment
                    ->getAirports()
                        ->setDeparture(new RemarksResponse\Location())
                        ->getDeparture()
                            ->setIata($departure);
            }

            if ($arrival !== null) {
                $legSegment
                    ->getAirports()
                        ->setArrival(new RemarksResponse\Location())
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

            $legSegment->setCarriers(new RemarksResponse\Carriers());

            if ($marketingCarrier !== null) {
                $legSegment
                    ->getCarriers()
                        ->setMarketing(new RemarksResponse\Carrier())
                        ->getMarketing()
                            ->setIata($marketingCarrier);
            }

            if ($operatingCarrier !== null) {
                $legSegment
                    ->getCarriers()
                        ->setOperating(new RemarksResponse\Carrier())
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
                    $legSegment->setBaggageRules(new RemarksResponse\BaggageRules());
                    $legSegment->getBaggageRules()
                        ->setWeight($baggageDetails->freeAllowance)
                        ->setUnit('kg');
                } elseif ($baggageDetails->quantityCode === 'N') {
                    $legSegment->setBaggageRules(new RemarksResponse\BaggageRules());
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
            /** @var RemarksResponse\Segment $legSegment */
            $legSegment = $itineraryLeg
                ->getSegments()
                ->offsetGet($segmentIndex);

            if (CabinClass::code($segmentFare)) {
                $legSegment
                    ->setCabinClass(new RemarksResponse\CabinClass())
                    ->getCabinClass()
                    ->setCode(CabinClass::code($segmentFare))
                    ->setName(CabinClass::name($segmentFare));
            }

            $legSegment->setGdsInformation(new RemarksResponse\AmadeusSegmentGdsInformation());
            $legSegment->getGdsInformation()->setResBookDesigCode(CabinClass::rbd($segmentFare));

            // add remaining seats
            $legSegment->setRemainingSeats($segmentFare->productInformation->cabinProduct->avlStatus);
        }

        return $itineraryLeg;
    }
}
