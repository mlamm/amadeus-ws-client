<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrUserIdentificationOwn;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * OriginIdentification Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class OriginIdentification extends AbstractModel
{
    /**
     * @var string
     */
    private $inHouseIdentification1;

    /**
     * @var string|null
     */
    private $originatorId;

    /**
     * @return string
     */
    public function getInHouseIdentification1() : ?string
    {
        return $this->inHouseIdentification1;
    }

    /**
     * @param string $inHouseIdentification1
     *
     * @return OriginIdentification
     */
    public function setInHouseIdentification1(string $inHouseIdentification1) : OriginIdentification
    {
        $this->inHouseIdentification1 = $inHouseIdentification1;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginatorId() : ?string
    {
        return $this->originatorId;
    }

    /**
     * @param string $originatorId
     *
     * @return OriginIdentification
     */
    public function setOriginatorId(string $originatorId) : OriginIdentification
    {
        $this->originatorId = $originatorId;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return $this
     */
    public function populate(\stdClass $data) : OriginIdentification
    {
        $this->inHouseIdentification1 = $data->{'inHouseIdentification1'} ?? null;
        $this->originatorId           = $data->{'originatorId'} ?? null;
        return $this;
    }
}
