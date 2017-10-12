<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;

/**
 * LegIndex.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class LegIndex
{
    /**
     * @var \stdClass[]
     */
    private $flightIndex;

    public function __construct(Result $amadeusResult)
    {
        $this->flightIndex = [];

        foreach (new NodeList($amadeusResult->response->flightIndex) as $flightIndex) {
            foreach (new NodeList($flightIndex->groupOfFlights) as $groupOfFlights) {
                $flightRefNumber = (new NodeList($groupOfFlights->propFlightGrDetail->flightProposal))->first()->ref;

                $this->flightIndex[][$flightRefNumber] = $groupOfFlights;
            }
        }
    }

    /**
     * Iterates over the <flightIndex> nodes identified by the parameter
     *
     * @param SegmentFlightRef $segmentFlightRef
     *
     * @return \Generator
     */
    public function groupOfFlights(SegmentFlightRef $segmentFlightRef) : \Generator
    {
        foreach ($segmentFlightRef->getSegmentRefNumbers() as $legIndex => $flightRefNumber) {
            yield $this->flightIndex[$legIndex][$flightRefNumber];
        }
    }
}
