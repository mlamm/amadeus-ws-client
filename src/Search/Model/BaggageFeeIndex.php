<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client\Result;

/**
 * BaggageFeeIndex.php
 *
 * @copyright Copyright (c) 2018 Invia Flights GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class BaggageFeeIndex
{
    const QUALIFIER_RECOMMENDATION = 'OC';

    const QUALIFIER_AMOUNT = 'OCM';

    const QUALIFIER_COVERAGE = 'OCC';

    /**
     * This index contains the reference number for the <freeBagAllowanceGrp>
     *
     * @var int[][]
     */
    private $serviceFeeReferenceGroup;

    /**
     * @var array[][][]
     */
    private $baggageFeeGroup;

    /**
     * @var float[]
     */
    private $amountReference;

    /**
     * @var float[]|null[]
     */
    private $descriptionReference;

    /**
     * BaggageFeeIndex constructor.
     *
     * @param Result $amaResult
     */
    public function __construct(Result $amaResult)
    {
        $this->build($amaResult->response);
    }

    /**
     * @param \stdClass $response
     *
     * @return $this
     */
    private function build(\stdClass $response)
    {
        if (isset($response->serviceFeesGrp)) {
            foreach (new NodeList($response->serviceFeesGrp) as $kas => $serviceFeesGrp) {
                if (($serviceFeesGrp->serviceTypeInfo->carrierFeeDetails->type ?? null) === self::QUALIFIER_RECOMMENDATION) {
                    $this->serviceFeeReferenceGroup = $this->buildServiceFeeReferenceGroup($serviceFeesGrp);
                    $this->amountReference          = $this->buildAmountReference($serviceFeesGrp);
                    $this->descriptionReference     = $this->buildDescriptionReference($serviceFeesGrp);
                    $this->baggageFeeGroup          = $this->buildBaggageFeeGroup($serviceFeesGrp);
                }
            }
        }

        return $this;
    }

    /**
     * @param \stdClass $serviceFeesGrp
     *
     * @return array
     */
    private function buildServiceFeeReferenceGroup(\stdClass $serviceFeesGrp): array
    {
        $refToGroupOfFlightsList = [];
        foreach (new NodeList($serviceFeesGrp->serviceFeeRefGrp) as $serviceFeeRefGrp) {
            foreach ($serviceFeeRefGrp as $serviceFeeRef) {
                $referencingDetails = [];
                foreach ($serviceFeeRef->referencingDetail as $referencingDetail) {
                    $referencingDetails[$referencingDetail->refQualifier] = (int)$referencingDetail->refNumber;
                }
                $refToGroupOfFlightsList[$referencingDetails[self::QUALIFIER_COVERAGE]] = $referencingDetails;
            }
        }

        return $refToGroupOfFlightsList;
    }

    /**
     * Build the index which contains the baggageDetails
     *
     * @param \stdClass $serviceFeesGrp
     *
     * @return array
     */
    private function buildBaggageFeeGroup(\stdClass $serviceFeesGrp): array
    {
        $baggageFeeGroup = [];
        foreach (new NodeList($serviceFeesGrp->serviceCoverageInfoGrp) as $serviceCoverageInfoGrp) {
            $coverageReference = (int)$serviceCoverageInfoGrp->itemNumberInfo->itemNumber->number;
            $coverageSegments  = $this->calculateCoverageSegments(new NodeList($serviceCoverageInfoGrp->serviceCovInfoGrp->coveragePerFlightsInfo));
            foreach (new NodeList($serviceCoverageInfoGrp->serviceCovInfoGrp->coveragePerFlightsInfo) as $coveragePerFlightsInfo) {
                $segmentNumber = (int)$coveragePerFlightsInfo->numberOfItemsDetails->refNum;
                foreach (new NodeList($coveragePerFlightsInfo->lastItemsDetails) as $lastItemsDetail) {
                    $legNumber = (int)$lastItemsDetail->refOfLeg;

                    $baggageFeeGroup[$coverageReference][$segmentNumber][$legNumber] = null;
                    if (isset($this->serviceFeeReferenceGroup[$coverageReference][self::QUALIFIER_AMOUNT], $this->amountReference[$this->serviceFeeReferenceGroup[$coverageReference][self::QUALIFIER_AMOUNT]])) {
                        $descriptionReference                                            = (int)$serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail->refNumber;
                        $totalFee                                                        = $this->amountReference[$this->serviceFeeReferenceGroup[$coverageReference][self::QUALIFIER_AMOUNT]];
                        $baggageFeeGroup[$coverageReference][$segmentNumber][$legNumber] = [
                            'fee'    => ($totalFee / $coverageSegments),
                            'weight' => $this->descriptionReference[$descriptionReference]['weight'] ?? null,
                            'unit'   => $this->descriptionReference[$descriptionReference]['unit'] ?? null,
                        ];
                    }
                }
            }
        }

        return $baggageFeeGroup;
    }

    /**
     * @param \stdClass $serviceFeesGrp
     *
     * @return array
     */
    private function buildAmountReference(\stdClass $serviceFeesGrp): array
    {
        $amountReference = [];
        foreach (new NodeList($serviceFeesGrp->serviceFeeInfoGrp) as $serviceFeeInfoGrp) {
            $amountReferenceId = (int)$serviceFeeInfoGrp->itemNumberInfo->itemNumber->number;
            if (false === empty($serviceFeeInfoGrp->serviceDetailsGrp->serviceMatchedInfoGroup->amountInfo)) {
                foreach (new NodeList($serviceFeeInfoGrp->serviceDetailsGrp->serviceMatchedInfoGroup->amountInfo) as $amountInfo) {
                    if (false === empty($amountInfo->monetaryDetail->amount)) {
                        $amountReference[$amountReferenceId] = (float)$amountInfo->monetaryDetail->amount;
                        break;
                    }
                }
            }
        }

        return $amountReference;
    }

    /**
     * @param \stdClass $serviceFeesGrp
     *
     * @return array
     */
    private function buildDescriptionReference(\stdClass $serviceFeesGrp): array
    {
        $descriptionReference = [];
        foreach (new NodeList($serviceFeesGrp->serviceDetailsGrp) as $serviceDetailsGrp) {
            $descriptionReferenceId = (int)$serviceDetailsGrp->feeDescriptionGrp->itemNumberInfo->itemNumberDetails->number;
            if (false === empty($serviceDetailsGrp->feeDescriptionGrp->commercialName->freeText)) {
                $description = null;
                if (preg_match('@(?<weight>\d+)(?<unit>KG|KILOGRAMS)@',
                    $serviceDetailsGrp->feeDescriptionGrp->commercialName->freeText,
                    $matches)) {
                    $description = [
                        'weight' => (float)$matches['weight'],
                        'unit'   => $matches['unit']
                    ];
                }
                $descriptionReference[$descriptionReferenceId] = $description;
            }
        }

        return $descriptionReference;
    }

    /**
     * @param $baggageRefNumber
     * @param $legOffset
     * @param $segmentOffset
     *
     * @return array|null
     */
    public function getBaggageFeeInfo($baggageRefNumber, $legOffset, $segmentOffset): ?array
    {
        return $this->baggageFeeGroup[$baggageRefNumber][$legOffset][$segmentOffset] ?? null;
    }

    /**
     * @param NodeList $coveragePerFlightsInfoList
     *
     * @return int
     */
    private function calculateCoverageSegments(NodeList $coveragePerFlightsInfoList): int
    {
        $coverageSegments = 0;
        foreach ($coveragePerFlightsInfoList as $coveragePerFlightsInfo) {
            $coverageSegments += (new NodeList($coveragePerFlightsInfo->lastItemsDetails))->count();
        }

        return $coverageSegments;
    }
}