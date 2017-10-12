<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

/**
 * FlightProposals.php
 *
 * Provides easy access to the various <flightProposal> values
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FlightProposals
{
    /**
     * Majority Carrier
     */
    private const UNIT_MCX = 'MCX';

    /**
     * Elapse Flying Time
     */
    private const UNIT_EFT = 'EFT';

    /**
     * @var array
     */
    private $proposals = [];

    /**
     * @param iterable $proposals
     */
    public function __construct(iterable $proposals)
    {
        foreach ($proposals as $proposal) {
            if (isset($proposal->unitQualifier)) {
                $this->proposals[$proposal->unitQualifier] = $proposal;
            }
        }
    }

    /**
     * Named constructor to build the object directly from a <groupOfFlights> node
     *
     * @param \stdClass $groupOfFlights
     * @return FlightProposals
     */
    public static function fromGroupOfFlights(\stdClass $groupOfFlights) : self
    {
        if (!isset($groupOfFlights->propFlightGrDetail->flightProposal)) {
            return new static([]);
        }

        return new static(new NodeList($groupOfFlights->propFlightGrDetail->flightProposal));
    }

    /**
     * @return bool
     */
    public function hasMajorityCarrier() : bool
    {
        return isset($this->proposals[self::UNIT_MCX]->ref);
    }

    /**
     * @return string
     */
    public function getMajorityCarrier() : string
    {
        return $this->proposals[self::UNIT_MCX]->ref;
    }

    /**
     * @return bool
     */
    public function hasElapsedFlyingTime() : bool
    {
        return isset($this->proposals[self::UNIT_EFT]->ref);
    }

    /**
     * @return int
     */
    public function getElapsedFlyingTime() : int
    {
        $estimatedFlightTime = $this->proposals[self::UNIT_EFT]->ref;

        $hours = (integer) substr($estimatedFlightTime, 0, 2);
        $minutes = (integer) substr($estimatedFlightTime, 2, 2);

        return $hours * 60 * 60 + $minutes * 60;
    }
}
