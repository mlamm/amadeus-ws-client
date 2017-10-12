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
    private $serviceCoverageInfoGrpIndex;

    private $freeBagAllowanceGrpIndex;

    public function __construct(Result $amaResult)
    {
        $this->build($amaResult->response);
    }

    public function hasFreeBagAllowanceInfo($refFromSegmentFlightRef, $flightNumber, $segmentNumber) : bool
    {
        return
            isset($this->serviceCoverageInfoGrpIndex[$refFromSegmentFlightRef][$flightNumber][$segmentNumber])
            && isset(
                $this->freeBagAllowanceGrpIndex[
                    $this->serviceCoverageInfoGrpIndex[$refFromSegmentFlightRef][$flightNumber][$segmentNumber]
                ]
            );
    }

    public function getFreeBagAllowanceInfo($refFromSegmentFlightRef, $flightNumber, $segmentNumber) : \stdClass
    {
        return
            $this->freeBagAllowanceGrpIndex[
                $this->serviceCoverageInfoGrpIndex[$refFromSegmentFlightRef][$flightNumber][$segmentNumber]
            ];
    }

    private function build(\stdClass $response)
    {
        if (isset($response->serviceFeesGrp)) {
            foreach (new NodeList($response->serviceFeesGrp) as $serviceFeesGrp) {
                if (($serviceFeesGrp->serviceTypeInfo->carrierFeeDetails->type ?? null) === 'FBA') {
                    $this->serviceCoverageInfoGrpIndex = $this->buildServiceCoverageInfoGrp(
                        new NodeList($serviceFeesGrp->serviceCoverageInfoGrp)
                    );
                    $this->freeBagAllowanceGrpIndex = $this->buildFreeBaggageAllowanceGrp(
                        new NodeList($serviceFeesGrp->freeBagAllowanceGrp)
                    );
                    break;
                }
            }
        }
    }

    private function buildServiceCoverageInfoGrp(iterable $serviceCoverageInfoGrps) : array
    {
        $serviceCoverageInfoGrpByRef = [];

        foreach ($serviceCoverageInfoGrps as $serviceCoverageInfoGrp) {
            $key = $serviceCoverageInfoGrp->itemNumberInfo->itemNumber->number;

            $serviceCoverageInfoGrpByRef[$key]
                = $this->buildServiceCovInfoGrp($serviceCoverageInfoGrp);
        }

        return $serviceCoverageInfoGrpByRef;
    }

    private function buildFreeBaggageAllowanceGrp(iterable $freeBagAllowanceGrps) : array
    {
        $freeBagAllowanceGrpByRef = [];

        foreach ($freeBagAllowanceGrps as $freeBagAllowanceGrp) {
            $key = $freeBagAllowanceGrp->itemNumberInfo->itemNumberDetails->number;

            $freeBagAllowanceGrpByRef[$key] = $freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails;
        }

        return $freeBagAllowanceGrpByRef;
    }

    private function buildServiceCovInfoGrp(\stdClass $serviceCoverageInfoGrp) : array
    {
        if (is_object($serviceCoverageInfoGrp->serviceCovInfoGrp)
            && isset($serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail)
            && $serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail->refQualifier = 'F'
        ) {
            return $this->buildCoveragePerFlightsInfo(
                new NodeList($serviceCoverageInfoGrp->serviceCovInfoGrp->coveragePerFlightsInfo),
                $serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail->refNumber
            );
        }

        return [];
    }

    private function buildCoveragePerFlightsInfo(
        iterable $coveragePerFlightsInfos,
        string $freeBagAllowanceRefNumber
    ) : array {
        $coveragePerFlightsInfoByRef = [];

        foreach ($coveragePerFlightsInfos as $coveragePerFlightsInfo) {
            if ($coveragePerFlightsInfo->numberOfItemsDetails->referenceQualifier === 'RS') {
                $refNum = $coveragePerFlightsInfo->numberOfItemsDetails->refNum;
                $coveragePerFlightsInfoByRef[$refNum] = [];

                foreach (new NodeList($coveragePerFlightsInfo->lastItemsDetails) as $lastItemsDetails) {
                    $coveragePerFlightsInfoByRef[$refNum][$lastItemsDetails->refOfLeg]
                        = $freeBagAllowanceRefNumber;
                }
            }
        }

        return $coveragePerFlightsInfoByRef;
    }
}
