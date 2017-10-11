<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Codeception\Util\Debug;

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
     * @var array
     */
    private $flights;

    /**
     * @param iterable $segmentFlightRefs
     */
    public function __construct(iterable $segmentFlightRefs)
    {
        $this->flights = $this->extractFlights($segmentFlightRefs);
    }

    /**
     * Build the index
     *
     * @param iterable $segmentFlightRefs
     *
     * @return array
     */
    private function extractFlights(iterable $segmentFlightRefs) : array
    {
        $flights = [];

        foreach ($segmentFlightRefs as $segmentFlightRef) {
            if (isset($segmentFlightRef->referencingDetail)) {
                $refs = (new NodeList($segmentFlightRef->referencingDetail))->toArray();
            }
            else {
                $refs = [];
            }

            if (isset($refs[0]->refQualifier) && $refs[0]->refQualifier === SegmentFlightRef::SEGMENT_REF_QUALIFIER) {
                $flights[] = new SegmentFlightRef($refs);
            }
        }

        return $flights;
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
     * @return SegmentFlightRef[]
     */
    public function getSegmentRefsForFlights() : array
    {
        return $this->flights;
    }
}
