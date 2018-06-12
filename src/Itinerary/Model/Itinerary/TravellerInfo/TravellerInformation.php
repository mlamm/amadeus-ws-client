<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Class Description
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class TravellerInformation extends AbstractModel
{
    /**
     * @var Traveller
     */
    private $traveller;

    /**
     * @var Passenger
     */
    private $passenger;

    /**
     * TravellerInformation constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->traveller   = new Traveller();
        $this->passenger   = new Passenger();

        parent::__construct($data);
    }

    /**
     * @return Traveller
     */
    public function getTraveller() : ?Traveller
    {
        return $this->traveller;
    }

    /**
     * @param Traveller $traveller
     *
     * @return TravellerInformation
     */
    public function setTraveller(Traveller $traveller) : TravellerInformation
    {
        $this->traveller = $traveller;
        return $this;
    }

    /**
     * @return Passenger
     */
    public function getPassenger() : ?Passenger
    {
        return $this->passenger;
    }

    /**
     * @param Passenger $passenger
     *
     * @return TravellerInformation
     */
    public function setPassenger(Passenger $passenger) : TravellerInformation
    {
        $this->passenger = $passenger;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->traveller)) {
            $this->traveller->populate($data->{'traveller'});
        }
        if (isset($data->passenger)) {
            $this->passenger->populate($data->{'passenger'});
        }
    }
}
