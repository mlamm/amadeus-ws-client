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
    public function getSegmentRefNumbers() : array
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
}