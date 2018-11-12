<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ArrayCollection
     */
    private $reservationInfo;

    /**
     * PnrHeader constructor.
     *
     * @param \stdClass|null $data optional, if set automatically populate data
     */
    public function __construct($data = null)
    {
        $this->reservationInfo = new ArrayCollection();
        if (null != $data) {
            $this->populate($data);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getReservationInfo() : ?ArrayCollection
    {
        return $this->reservationInfo;
    }

    /**
     * @param ArrayCollection $reservationInfo
     *
     * @return PnrHeader
     */
    public function setReservationInfo(ArrayCollection $reservationInfo) : PnrHeader
    {
        $this->reservationInfo = $reservationInfo;
        return $this;
    }

    /**
     * Populate from stdClass
     *
     * @param array|\stdClass $data
     */
    public function populate($data)
    {
        if (isset($data->reservationInfo)) {
            if (is_array($data)) {
                foreach ($data as $reservation) {
                    $this->reservationInfo->add(
                        (new Reservation())->populate($reservation->{'reservationInfo'}->{'reservation'})
                    );
                }
            } else {
                $this->reservationInfo->add((new Reservation())->populate($data->{'reservationInfo'}->{'reservation'}));
            }
        }
    }
}
