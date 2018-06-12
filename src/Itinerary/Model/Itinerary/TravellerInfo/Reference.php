<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Reference Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Reference extends AbstractModel
{
    /**
     * @var string
     */
    private $qualifier;

    /**
     * @var string
     */
    private $number;

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
     * @return Reference
     */
    public function setQualifier(string $qualifier) : Reference
    {
        $this->qualifier = $qualifier;
        return $this;
    }

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
     * @return Reference
     */
    public function setNumber(string $number) : Reference
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->qualifier = $data->{'qualifier'} ?? null;
        $this->number    = $data->{'number'} ?? null;
    }
}
