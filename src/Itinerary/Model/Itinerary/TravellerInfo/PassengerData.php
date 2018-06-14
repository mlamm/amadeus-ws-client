<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * PassengerData Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class PassengerData extends AbstractModel
{
    /**
     * @var DateAndTimeDetails
     */
    private $dateOfBirth;

    /**
     * @var TravellerInformation
     */
    private $travellerInformation;

    /**
     * PassengerData constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->travellerInformation = new TravellerInformation();
        $this->dateOfBirth          = new DateAndTimeDetails();

        parent::__construct($data);
    }

    /**
     * @return TravellerInformation
     */
    public function getTravellerInformation() : ?TravellerInformation
    {
        return $this->travellerInformation;
    }

    /**
     * @param TravellerInformation $travellerInformation
     *
     * @return PassengerData
     */
    public function setTravellerInformation(TravellerInformation $travellerInformation) : PassengerData
    {
        $this->travellerInformation = $travellerInformation;
        return $this;
    }

    /**
     * @return DateAndTimeDetails
     */
    public function getDateOfBirth() : ?DateAndTimeDetails
    {
        return $this->dateOfBirth;
    }

    /**
     * @param DateAndTimeDetails $dateOfBirth
     *
     * @return PassengerData
     */
    public function setDateOfBirth(DateAndTimeDetails $dateOfBirth) : PassengerData
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->travellerInformation)) {
            $this->travellerInformation->populate($data->{'travellerInformation'});
        }
        if (isset($data->dateOfBirth) && isset($data->{'dateOfBirth'}->dateAndTimeDetails)) {
            $this->dateOfBirth->populate($data->{'dateOfBirth'}->{'dateAndTimeDetails'});
        }
    }
}
