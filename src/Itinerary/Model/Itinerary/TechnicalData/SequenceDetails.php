<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * SequenceDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SequenceDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $number;

    /**
     * @return string
     */
    public function getNumber() : ?string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return SequenceDetails
     */
    public function setNumber(string $number) : SequenceDetails
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
     */
    public function populate(\stdClass $data)
    {
        $this->number= $data->number ?? null;
    }
}
