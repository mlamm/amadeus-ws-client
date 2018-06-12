<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * ArrivalStationInfo Description
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ArrivalStationInfo extends AbstractModel
{
    /**
     * @var string
     */
    private $terminal;

    /**
     * @return string
     */
    public function getTerminal() : ?string
    {
        return $this->terminal;
    }

    /**
     * @param string $terminal
     *
     * @return ArrivalStationInfo
     */
    public function setTerminal(string $terminal) : ArrivalStationInfo
    {
        $this->terminal = $terminal;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->terminal = $data->{'terminal'} ?? null;
    }
}
