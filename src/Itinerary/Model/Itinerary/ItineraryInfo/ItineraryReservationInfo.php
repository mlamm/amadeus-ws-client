<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ReservationInfo\Reservation;

/**
 * ItineraryReservationInfo Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryReservationInfo extends AbstractModel
{
    /**
     * @var Reservation
     */
    private $reservation;

    /**
     * ItineraryReservationInfo constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->reservation = new Reservation();
        parent::__construct($data);
    }

    /**
     * @return Reservation
     */
    public function getReservation() : ?Reservation
    {
        return $this->reservation;
    }

    /**
     * @param Reservation $reservation
     *
     * @return ItineraryReservationInfo
     */
    public function setReservation(Reservation $reservation) : ItineraryReservationInfo
    {
        $this->reservation = $reservation;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->reservation)) {
            $this->reservation->populate($data->{'reservation'});
        }
    }
}
