<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * ProductDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ProductDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $equipment;

    /**
     * @var string
     */
    private $numOfStops;

    /**
     * @var string
     */
    private $weekDay;

    /**
     * @return string
     */
    public function getEquipment() : ?string
    {
        return $this->equipment;
    }

    /**
     * @param string $equipment
     *
     * @return ProductDetails
     */
    public function setEquipment(string $equipment) : ProductDetails
    {
        $this->equipment = $equipment;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumOfStops() : ?string
    {
        return $this->numOfStops;
    }

    /**
     * @param string $numOfStops
     *
     * @return ProductDetails
     */
    public function setNumOfStops(string $numOfStops) : ProductDetails
    {
        $this->numOfStops = $numOfStops;
        return $this;
    }

    /**
     * @return string
     */
    public function getWeekDay() : ?string
    {
        return $this->weekDay;
    }

    /**
     * @param string $weekDay
     *
     * @return ProductDetails
     */
    public function setWeekDay(string $weekDay) : ProductDetails
    {
        $this->weekDay = $weekDay;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->equipment  = $data->{'equipment'} ?? null;
        $this->numOfStops = $data->{'numOfStops'} ?? null;
        $this->weekDay    = $data->{'weekDay'} ?? null;
    }
}
