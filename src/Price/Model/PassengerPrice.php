<?php

namespace Flight\Service\Amadeus\Price\Model;

/**
 * PassengerPrice class.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class PassengerPrice
{
    /**
     * @var float
     */
    private $totalTax;

    /**
     * @var float
     */
    private $equivFare;

    /**
     * @var float
     */
    private $equivFareCurrency;

    /**
     * @var float
     */
    private $baseFare;

    /**
     * @var string
     */
    private $baseFareCurrency;

    /**
     * @return float
     */
    public function getTotalTax() : ?float
    {
        return $this->totalTax;
    }

    /**
     * @param float $totalTax
     *
     * @return PassengerPrice
     */
    public function setTotalTax(float $totalTax) : PassengerPrice
    {
        $this->totalTax = $totalTax;
        return $this;
    }

    /**
     * @return float
     */
    public function getEquivFare() : ?float
    {
        return $this->equivFare;
    }

    /**
     * @param float $equivFare
     *
     * @return PassengerPrice
     */
    public function setEquivFare(float $equivFare) : PassengerPrice
    {
        $this->equivFare = $equivFare;
        return $this;
    }

    /**
     * @return float
     */
    public function getEquivFareCurrency() : ?float
    {
        return $this->equivFareCurrency;
    }

    /**
     * @param float $equivFareCurrency
     *
     * @return PassengerPrice
     */
    public function setEquivFareCurrency(float $equivFareCurrency) : PassengerPrice
    {
        $this->equivFareCurrency = $equivFareCurrency;
        return $this;
    }

    /**
     * @return float
     */
    public function getBaseFare() : ?float
    {
        return $this->baseFare;
    }

    /**
     * @param float $baseFare
     *
     * @return PassengerPrice
     */
    public function setBaseFare(float $baseFare) : PassengerPrice
    {
        $this->baseFare = $baseFare;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseFareCurrency() : ?string
    {
        return $this->baseFareCurrency;
    }

    /**
     * @param string $baseFareCurrency
     *
     * @return PassengerPrice
     */
    public function setBaseFareCurrency(string $baseFareCurrency) : PassengerPrice
    {
        $this->baseFareCurrency = $baseFareCurrency;
        return $this;
    }
}
