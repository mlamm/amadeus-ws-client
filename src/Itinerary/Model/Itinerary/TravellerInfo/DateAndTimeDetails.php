<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * DateAndTimeDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class DateAndTimeDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $qualifier;

    /**
     * @var string
     */
    private $date;

    /**
     * @return string
     */
    public function getQualifier() : ?string
    {
        return $this->qualifier;
    }

    /**
     * @param string $qualifier
     *
     * @return DateAndTimeDetails
     */
    public function setQualifier(string $qualifier) : DateAndTimeDetails
    {
        $this->qualifier = $qualifier;
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
     * @return DateAndTimeDetails
     */
    public function setDate(string $date) : DateAndTimeDetails
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->qualifier= $data->qualifier ?? null;
        $this->date     = $data->date ?? null;
    }
}
