<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var PassengerData[]|ArrayCollection
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
        $this->passengerData              = new ArrayCollection();

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
     * @return PassengerData[]|ArrayCollection
     */
    public function getPassengerData() : ?ArrayCollection
    {
        return $this->passengerData;
    }

    /**
     * @param ArrayCollection $passengerData
     *
     * @return TravellerInfo
     */
    public function setPassengerData(ArrayCollection $passengerData) : TravellerInfo
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
            $this->populatePassengerData($data->passengerData);
        }
    }

    /**
     * @param \stdClass|array $passengers
     */
    protected function populatePassengerData($passengers): void
    {
        // passenger with baby
        if (is_array($passengers)) {
            foreach ($passengers as $passenger) {
                $passengerData = new PassengerData();
                $passengerData->populate($passenger);
                $this->passengerData->add($passengerData);
            }
            // passenger without baby
        } else {
            $passengerData = new PassengerData();
            $passengerData->populate($passengers);
            $this->passengerData->add($passengerData);
        }
    }
}
