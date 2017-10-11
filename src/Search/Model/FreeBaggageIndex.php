<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Codeception\Util\Debug;

/**
 * FreeBaggageIndex.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FreeBaggageIndex
{
    private $serviceCoverageInfoGrpIndex;

    private $freeBagAllowanceGrpIndex;

    public function __construct(\stdClass $response)
    {
        $this->build($response);
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

        Debug::debug($this->serviceCoverageInfoGrpIndex);
        Debug::debug($this->freeBagAllowanceGrpIndex);
    }

    private function buildServiceCoverageInfoGrp(iterable $serviceCoverageInfoGrps) : array
    {
        $serviceCoverageInfoGrpByRef = [];

        foreach ($serviceCoverageInfoGrps as $serviceCoverageInfoGrp) {
            $serviceCoverageInfoGrpByRef[$this->serviceCoverageInfoGrpKey($serviceCoverageInfoGrp)]
                = $this->buildServiceCovInfoGrp(new NodeList($serviceCoverageInfoGrp->serviceCovInfoGrp));
        }

        return $serviceCoverageInfoGrpByRef;
    }

    private function serviceCoverageInfoGrpKey(\stdClass $serviceCoverageInfoGrp) : string
    {
        return $serviceCoverageInfoGrp->itemNumberInfo->itemNumber->number;
    }

    private function buildFreeBaggageAllowanceGrp(iterable $freeBagAllowanceGrps) : array
    {
        $freeBagAllowanceGrpByRef = [];

        foreach ($freeBagAllowanceGrps as $freeBagAllowanceGrp) {
            $freeBagAllowanceGrpByRef[$this->freeBagAllowanceGrpKey($freeBagAllowanceGrp)]
                = $freeBagAllowanceGrp;
        }

        return $freeBagAllowanceGrpByRef;
    }

    private function freeBagAllowanceGrpKey(\stdClass $freeBagAllowanceGrp) : string
    {
        return $freeBagAllowanceGrp->itemNumberInfo->itemNumberDetails->number;
    }

    private function buildServiceCovInfoGrp(iterable $serviceCovInfoGrps) : array
    {
        $serviceCovInfoGrpByRef = [];

        foreach ($serviceCovInfoGrps as $serviceCovInfoGrp) {
            $serviceCovInfoGrpByRef[$this->serviceCovInfoGrpKey($serviceCovInfoGrp)]
                = $this->buildCoveragePerFlightsInfo(new NodeList($serviceCovInfoGrp->coveragePerFlightsInfo));
        }

        return $serviceCovInfoGrpByRef;
    }

    private function serviceCovInfoGrpKey(\stdClass $serviceCovInfoGrp) : string
    {
        return $serviceCovInfoGrp->refInfo->referencingDetail->refQualifier
            . ':' . $serviceCovInfoGrp->refInfo->referencingDetail->refNumber;
    }

    private function buildCoveragePerFlightsInfo(iterable $coveragePerFlightsInfos) : array
    {
        $coveragePerFlightsInfoByRef = [];

        foreach ($coveragePerFlightsInfos as $coveragePerFlightsInfo) {
            foreach (new NodeList($coveragePerFlightsInfo->lastItemsDetails) as $lastItemsDetails) {
                $coveragePerFlightsInfoByRef[$this->coveragePerInfoGrpKey($coveragePerFlightsInfo, $lastItemsDetails)]
                    = $coveragePerFlightsInfo;
            }
        }

        return $coveragePerFlightsInfoByRef;
    }

    private function coveragePerInfoGrpKey(\stdClass $coveragePerFlightsInfo, \stdClass $lastItemsDetails) : string
    {
        return $coveragePerFlightsInfo->numberOfItemsDetails->referenceQualifier
            . ':' . $coveragePerFlightsInfo->numberOfItemsDetails->refNum
            . ':' . $lastItemsDetails->refOfLeg;
    }
}
