<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;

/**
 * LegIndex.php
 *
 * Provides easy access to <groupOfFlights> nodes via refIds
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

    /**
     * @param Result $amadeusResult
     */
    public function __construct(Result $amadeusResult)
    {
        $this->flightIndex = [];

        foreach (new NodeList($amadeusResult->response->flightIndex) as $flightOffset => $flightIndex) {
            foreach (new NodeList($flightIndex->groupOfFlights) as $groupOfFlights) {
                $flightRefNumber = (new NodeList($groupOfFlights->propFlightGrDetail->flightProposal))->first()->ref;

                $this->flightIndex[$flightOffset][$flightRefNumber] = $groupOfFlights;
            }
        }
    }

    /**
     * @param string $legOffset             Offset of the <flightIndex> node (zero-based)
     * @param string $refToGroupOfFlights   Reference to the <groupOfFlights> node (starts at 1)
     *
     * @return \stdClass  The <groupOfFlights> node
     */
    public function groupOfFlights(string $legOffset, string $refToGroupOfFlights) : \stdClass
    {
        return $this->flightIndex[$legOffset][$refToGroupOfFlights];
    }
}
