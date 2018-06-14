<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Facilities Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Facilities extends AbstractModel
{
    /**
     * @var string
     */
    private $entertainement;

    /**
     * @var string
     */
    private $entertainementDescription;

    /**
     * @return string
     */
    public function getEntertainement() : ?string
    {
        return $this->entertainement;
    }

    /**
     * @param string $entertainement
     *
     * @return Facilities
     */
    public function setEntertainement(string $entertainement) : Facilities
    {
        $this->entertainement = $entertainement;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntertainementDescription() : ?string
    {
        return $this->entertainementDescription;
    }

    /**
     * @param string $entertainementDescription
     *
     * @return Facilities
     */
    public function setEntertainementDescription(string $entertainementDescription) : Facilities
    {
        $this->entertainementDescription = $entertainementDescription;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->entertainement           = $data->entertainement ?? null;
        $this->entertainementDescription= $data->entertainementDescription ?? null;
    }
}
