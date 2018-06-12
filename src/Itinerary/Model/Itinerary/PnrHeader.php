<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ReservationInfo\Reservation;

/**
 * PnrHeader Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class PnrHeader
{
    /**
     * @var Reservation
     */
    private $reservationInfo;

    /**
     * PnrHeader constructor.
     *
     * @param \stdClass|null $data optional, if set automatically populate data
     */
    public function __construct($data = null)
    {
        $this->reservationInfo = new Reservation();
        if (null != $data) {
            $this->populate($data);
        }
    }

    /**
     * @return Reservation
     */
    public function getReservationInfo() : ?Reservation
    {
        return $this->reservationInfo;
    }

    /**
     * @param Reservation $reservationInfo
     *
     * @return PnrHeader
     */
    public function setReservationInfo(Reservation $reservationInfo) : PnrHeader
    {
        $this->reservationInfo = $reservationInfo;
        return $this;
    }

    /**
     * Populate from stdClass
     *
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->reservationInfo)) {
            $this->reservationInfo->populate($data->{'reservationInfo'}->{'reservation'});
        }
    }
}
