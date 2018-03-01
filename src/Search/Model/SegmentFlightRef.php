<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

/**
 * SegmentFlightRef.php
 *
 * Provides easy access to the child nodes of the <segmentFlightRef> node
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SegmentFlightRef
{
    /**
     * Segment/service reference number
     */
    const SEGMENT_REF_QUALIFIER = 'S';

    /**
     * Baggage fee reference number
     */
    const FREE_BAGGAGE_ALLOWANCE_REF_QUALIFIER = 'B';

    /**
     * Baggage fee reference number
     */
    const BAGGAGE_REF_QUALIFIER = 'OC';

    /**
     * @var array
     */
    private $referencingDetails;

    /**
     * @param iterable $referencingDetails
     */
    public function __construct(iterable $referencingDetails)
    {
        // index <referencingDetail> nodes by their <refQualifier>
        foreach ($referencingDetails as $referencingDetail) {
            if (!isset($this->referencingDetails[$referencingDetail->refQualifier])) {
                $this->referencingDetails[$referencingDetail->refQualifier] = [];
            }

            $this->referencingDetails[$referencingDetail->refQualifier][] = $referencingDetail;
        }
    }

    /**
     * Returns the list of segment <refNumber> values
     *
     * @return array
     */
    public function getSegmentRefNumbers(): array
    {
        if (!isset($this->referencingDetails[self::SEGMENT_REF_QUALIFIER])) {
            return [];
        }

        $refNumbers = [];
        foreach ($this->referencingDetails[self::SEGMENT_REF_QUALIFIER] as $referencingDetail) {
            $refNumbers[] = $referencingDetail->refNumber;
        }

        return $refNumbers;
    }

    /**
     * Returns the free baggage allowance <refNumber> value
     *
     * @return int|null
     */
    public function getFreeBaggageAllowanceRefNumber():?int
    {
        $refNumber = null;
        if (true === isset($this->referencingDetails[self::FREE_BAGGAGE_ALLOWANCE_REF_QUALIFIER])) {
            $refNumber = (int)(new NodeList($this->referencingDetails[self::FREE_BAGGAGE_ALLOWANCE_REF_QUALIFIER]))
                ->first()->refNumber;
        }

        return $refNumber;
    }



    /**
     * Returns the baggage <refNumber> value
     *
     * @return int|null
     */
    public function getBaggageRefNumber(): ?int
    {
        $refNumber = null;
        if (true === isset($this->referencingDetails[self::BAGGAGE_REF_QUALIFIER])) {
            $refNumber = (int)(new NodeList($this->referencingDetails[self::BAGGAGE_REF_QUALIFIER]))
                ->first()->refNumber;
        }

        return $refNumber;
    }
}
