<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ReservationInfo;

/**
 * Reservation Model for Reservation Info
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Reservation
{
    /**
     * @var string
     */
    private $companyId;

    /**
     * @var string
     */
    private $controlNumber;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $time;

    /**
     * @return string
     */
    public function getCompanyId() : ?string
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     *
     * @return Reservation
     */
    public function setCompanyId(string $companyId) : Reservation
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getControlNumber() : ?string
    {
        return $this->controlNumber;
    }

    /**
     * @param string $controlNumber
     *
     * @return Reservation
     */
    public function setControlNumber(string $controlNumber) : Reservation
    {
        $this->controlNumber = $controlNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate() : ?string
    {
        return $this->date;
    }

    /**
     * @param string $date
     *
     * @return Reservation
     */
    public function setDate(string $date) : Reservation
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTime() : ?string
    {
        return $this->time;
    }

    /**
     * @param string $time
     *
     * @return Reservation
     */
    public function setTime(string $time) : Reservation
    {
        $this->time = $time;
        return $this;
    }

    /**
     * set data from stdClass
     *
     * @param \stdClass $data
     *
     * @return Reservation
     */
    public function populate(\stdClass $data) : Reservation
    {
        $this->companyId    = $data->companyId ?? null;
        $this->controlNumber= $data->controlNumber ?? null;
        $this->date         = $data->date ?? null;
        $this->time         = $data->time ?? null;

        return $this;
    }
}
