<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo\ElementManagementPassenger;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo\PassengerData;

/**
 * TravellerInfo Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class TravellerInfo extends AbstractModel
{
    /**
     * @var ElementManagementPassenger
     */
    private $elementManagementPassenger;

    /**
     * @var PassengerData
     */
    private $passengerData;

    /**
     * TravellerInfo constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->elementManagementPassenger = new ElementManagementPassenger();
        $this->passengerData              = new PassengerData();

        parent::__construct($data);
    }

    /**
     * @return ElementManagementPassenger
     */
    public function getElementManagementPassenger() : ?ElementManagementPassenger
    {
        return $this->elementManagementPassenger;
    }

    /**
     * @param ElementManagementPassenger $elementManagementPassenger
     *
     * @return TravellerInfo
     */
    public function setElementManagementPassenger(ElementManagementPassenger $elementManagementPassenger) : TravellerInfo
    {
        $this->elementManagementPassenger = $elementManagementPassenger;
        return $this;
    }

    /**
     * @return PassengerData
     */
    public function getPassengerData() : ?PassengerData
    {
        return $this->passengerData;
    }

    /**
     * @param PassengerData $passengerData
     *
     * @return TravellerInfo
     */
    public function setPassengerData(PassengerData $passengerData) : TravellerInfo
    {
        $this->passengerData = $passengerData;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->{'elementManagementPassenger'})) {
            $this->elementManagementPassenger->populate($data->{'elementManagementPassenger'});
        }
        if (isset($data->passengerData)) {
            $this->passengerData->populate($data->{'passengerData'});
        }
    }
}
