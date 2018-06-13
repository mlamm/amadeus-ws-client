<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Product Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Product extends AbstractModel
{
    /**
     * @var string
     */
    private $arrDate;

    /**
     * @var string
     */
    private $arrTime;

    /**
     * @var string
     */
    private $depDate;

    /**
     * @var string
     */
    private $depTime;

    /**
     * @return string
     */
    public function getArrDate() : ?string
    {
        return $this->arrDate;
    }

    /**
     * @param string $arrDate
     *
     * @return Product
     */
    public function setArrDate(string $arrDate) : Product
    {
        $this->arrDate = $arrDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getArrTime() : ?string
    {
        return $this->arrTime;
    }

    /**
     * @param string $arrTime
     *
     * @return Product
     */
    public function setArrTime(string $arrTime) : Product
    {
        $this->arrTime = $arrTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getDepDate() : ?string
    {
        return $this->depDate;
    }

    /**
     * @param string $depDate
     *
     * @return Product
     */
    public function setDepDate(string $depDate) : Product
    {
        $this->depDate = $depDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getDepTime() : ?string
    {
        return $this->depTime;
    }

    /**
     * @param string $depTime
     *
     * @return Product
     */
    public function setDepTime(string $depTime) : Product
    {
        $this->depTime = $depTime;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->arrDate= $data->arrDate ?? null;
        $this->arrTime= $data->arrTime ?? null;
        $this->depDate= $data->depDate ?? null;
        $this->depTime= $data->depTime ?? null;
    }
}
