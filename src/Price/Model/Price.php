<?php

namespace Flight\Service\Amadeus\Price\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Price Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Price
{
    /**
     * @var string
     */
    private $lastTicketingDate;

    /**
     * @var ArrayCollection
     */
    private $passengerPrice;

    /**
     * @var
     */
    private $validatingCarrier;

    /**
     * Price constructor.
     *
     */
    public function __construct()
    {
        $this->passengerPrice = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLastTicketingDate() : ?string
    {
        return $this->lastTicketingDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getPassengerPrice() : ArrayCollection
    {
        return $this->passengerPrice;
    }

    /**
     * @param ArrayCollection $passengerPrice
     *
     * @return Price
     */
    public function setPassengerPrice(ArrayCollection $passengerPrice) : Price
    {
        $this->passengerPrice = $passengerPrice;
        return $this;
    }

    /**
     * @param PassengerPrice $passengerPrice
     *
     * @return Price
     */
    public function addPassengerPrice(PassengerPrice $passengerPrice) : Price
    {
        $this->passengerPrice->add($passengerPrice);
        return $this;
    }

    /**
     * @param string $lastTicketingDate
     *
     * @return Price
     */
    public function setLastTicketingDate($lastTicketingDate) : Price
    {
        $this->lastTicketingDate = $lastTicketingDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidatingCarrier()
    {
        return $this->validatingCarrier;
    }

    /**
     * @param string $validatingCarrier
     *
     * @return Price
     */
    public function setValidatingCarrier($validatingCarrier)
    {
        $this->validatingCarrier = $validatingCarrier;
        return $this;
    }
}
