<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Ticket Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Ticket extends AbstractModel
{
    /**
     * @var string
     */
    private $indicator;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $officeId;

    /**
     * @return string
     */
    public function getIndicator() : ?string
    {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     *
     * @return Ticket
     */
    public function setIndicator(string $indicator) : Ticket
    {
        $this->indicator = $indicator;
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
     * @return Ticket
     */
    public function setDate(string $date) : Ticket
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getOfficeId() : ?string
    {
        return $this->officeId;
    }

    /**
     * @param string $officeId
     *
     * @return Ticket
     */
    public function setOfficeId(string $officeId) : Ticket
    {
        $this->officeId = $officeId;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->indicator= $data->indicator ?? null;
        $this->date     = $data->date ?? null;
        $this->officeId = $data->officeId ?? null;
    }
}
