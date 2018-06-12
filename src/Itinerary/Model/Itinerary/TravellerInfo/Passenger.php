<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Passenger Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Passenger extends AbstractModel
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getFirstName() : ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return Passenger
     */
    public function setFirstName(string $firstName) : Passenger
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Passenger
     */
    public function setType(string $type) : Passenger
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->firstName = $data->{'firstName'} ?? null;
        $this->type      = $data->{'type'} ?? null;
    }
}
