<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Traveller Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Traveller extends AbstractModel
{
    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @return string
     */
    public function getSurname() : ?string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return Traveller
     */
    public function setSurname(string $surname) : Traveller
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity() : ?string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     *
     * @return Traveller
     */
    public function setQuantity(string $quantity) : Traveller
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->surname  = $data->{'surname'} ?? null;
        $this->quantity = $data->{'quantity'} ?? null;
    }
}
