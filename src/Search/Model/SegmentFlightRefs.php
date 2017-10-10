<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

/**
 * SegmentFlightRefs.php
 *
 * Provides easy access to <segmentFlightRef> nodes via refQualifier
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SegmentFlightRefs
{
    /**
     * Segment/service reference number
     */
    private const SEGMENT_REF_QUALIFIER = 'S';

    /**
     * @var \stdClass[]
     */
    private $referencingDetails;

    /**
     * @param iterable $segmentFlightRefs
     */
    public function __construct(iterable $segmentFlightRefs)
    {
        $this->referencingDetails = $this->indexByQualifier($segmentFlightRefs);
    }

    /**
     * Build the index
     *
     * @param iterable $segmentFlightRefs
     *
     * @return array
     */
    private function indexByQualifier(iterable $segmentFlightRefs) : array
    {
        $segmentFlightRefsByQualifier = [];

        foreach ($segmentFlightRefs as $segmentFlightRef) {
            if (isset($segmentFlightRef->referencingDetail)) {
                $refs = (new NodeList($segmentFlightRef->referencingDetail))->toArray();
            }
            else {
                $refs = [];
            }

            if (isset($refs[0]->refQualifier)) {
                $segmentFlightRefsByQualifier[$refs[0]->refQualifier] = $refs;
            }
        }

        return $segmentFlightRefsByQualifier;
    }

    /**
     * Named constructor to build the object from a <recommendation> node
     *
     * @param \stdClass $recommendation
     *
     * @return SegmentFlightRefs
     */
    public static function fromRecommendation(\stdClass $recommendation) : self
    {
        if (!isset($recommendation->segmentFlightRef)) {
            return new static([]);
        }

        return new static(new NodeList($recommendation->segmentFlightRef));
    }

    /**
     * Returns the list of <refNumber> values
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
