<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;

/**
 * FreeBaggageIndex.php
 *
 * Builds an index over the free baggage information for easy query via the relevant references
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FreeBaggageIndex
{
    /**
     * This index contains the reference number for the <freeBagAllowanceGrp>
     *
     * @var array
     */
    private $serviceCoverageInfoGrpIndex;

    /**
     * This index contains the <baggageDetails> of all <freeBagAllowanceGrp>
     *
     * @var array
     */
    private $freeBagAllowanceGrpIndex;

    /**
     * @param Result $amaResult
     */
    public function __construct(Result $amaResult)
    {
        $this->build($amaResult->response);
    }

    /**
     * Query the index for baggage details
     *
     * @param string $refToGroupOfFlights
     * @param string $refToFlightIndex
     * @param string $refToFlightDetails
     *
     * @return null|\stdClass               The content of a <baggageDetail> node
     */
    public function getFreeBagAllowanceInfo(
        string $refToGroupOfFlights,
        string $refToFlightIndex,
        string $refToFlightDetails
    ): ?\stdClass {

        if (!isset($this->serviceCoverageInfoGrpIndex[$refToGroupOfFlights][$refToFlightIndex][$refToFlightDetails])) {
            return null;
        }

        $baggageNumber = $this->serviceCoverageInfoGrpIndex[$refToGroupOfFlights][$refToFlightIndex][$refToFlightDetails];

        if (!isset($this->freeBagAllowanceGrpIndex[$baggageNumber])) {
            return null;
        }

        return $this->freeBagAllowanceGrpIndex[$baggageNumber];
    }

    /**
     * Build the indices
     *
     * @param \stdClass $response
     */
    private function build(\stdClass $response) : void
    {
        if (isset($response->serviceFeesGrp)) {
            foreach (new NodeList($response->serviceFeesGrp) as $serviceFeesGrp) {
                if (($serviceFeesGrp->serviceTypeInfo->carrierFeeDetails->type ?? null) === 'FBA') {
                    $this->serviceCoverageInfoGrpIndex = $this->buildServiceCoverageInfoGrp($serviceFeesGrp);
                    $this->freeBagAllowanceGrpIndex = $this->buildFreeBaggageAllowanceGrp($serviceFeesGrp);
                    break;
                }
            }
        }
    }

    /**
     * Build the index which points to a specific <freeBagAllowance> node
     *
     * @param \stdClass $serviceFeesGrp
     * @return array
     */
    private function buildServiceCoverageInfoGrp(\stdClass $serviceFeesGrp) : array
    {
        $index = [];

        foreach (new NodeList($serviceFeesGrp->serviceCoverageInfoGrp) as $serviceCoverageInfoGrp) {
            $refToGroupOfFlights = $serviceCoverageInfoGrp->itemNumberInfo->itemNumber->number;

            foreach (new NodeList($serviceCoverageInfoGrp->serviceCovInfoGrp) as $serviceCovInfoGrp) {
                foreach (new NodeList($serviceCovInfoGrp->refInfo->referencingDetail) as $referencingDetail) {
                    $refToFreeBaggAllowance = $referencingDetail->refNumber;

                    foreach (new NodeList($serviceCovInfoGrp->coveragePerFlightsInfo) as $coveragePerFlightsInfo) {
                        foreach (new NodeList($coveragePerFlightsInfo->numberOfItemsDetails) as $numberOfItemsDetails) {
                            if ($numberOfItemsDetails->referenceQualifier === 'RS') {
                                $refToFlightIndex = $coveragePerFlightsInfo->numberOfItemsDetails->refNum;

                                foreach (new NodeList($coveragePerFlightsInfo->lastItemsDetails) as $lastItemsDetails) {
                                    $refToFlightDetails = $lastItemsDetails->refOfLeg;

                                    $index[$refToGroupOfFlights][$refToFlightIndex][$refToFlightDetails]
                                        = $refToFreeBaggAllowance;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $index;
    }

    /**
     * Build the index which contains the baggageDetails
     *
     * @param \stdClass $serviceFeesGrp
     * @return array
     */
    private function buildFreeBaggageAllowanceGrp(\stdClass $serviceFeesGrp) : array
    {
        $freeBagAllowanceGrpByRef = [];

        foreach (new NodeList($serviceFeesGrp->freeBagAllowanceGrp) as $freeBagAllowanceGrp) {
            $baggageNumber = $freeBagAllowanceGrp->itemNumberInfo->itemNumberDetails->number;

            $freeBagAllowanceGrpByRef[$baggageNumber] = $freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails;
        }

        return $freeBagAllowanceGrpByRef;
    }
}
