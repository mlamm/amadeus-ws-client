<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrPreferences;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrSystemDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrUserIdentificationOwn;

/**
 * SbrCreationPosDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrCreationPosDetails extends AbstractModel
{
    /**
     * @var SbrSystemDetails
     */
    private $sbrSystemDetails;
    /**
     * @var SbrUserIdentificationOwn
     */
    private $sbrUserIdentificationOwn;

    /**
     * @var SbrPreferences
     */
    private $sbrPreferences;

    /**
     * SbrCreationPosDetails constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->sbrSystemDetails         = new SbrSystemDetails();
        $this->sbrPreferences           = new SbrPreferences();
        $this->sbrUserIdentificationOwn = new SbrUserIdentificationOwn();

        parent::__construct($data);
    }

    /**
     * @return SbrSystemDetails
     */
    public function getSbrSystemDetails() : ?SbrSystemDetails
    {
        return $this->sbrSystemDetails;
    }

    /**
     * @param SbrSystemDetails $sbrSystemDetails
     *
     * @return SbrCreationPosDetails
     */
    public function setSbrSystemDetails(SbrSystemDetails $sbrSystemDetails) : SbrCreationPosDetails
    {
        $this->sbrSystemDetails = $sbrSystemDetails;
        return $this;
    }

    /**
     * @return SbrUserIdentificationOwn
     */
    public function getSbrUserIdentificationOwn() : ?SbrUserIdentificationOwn
    {
        return $this->sbrUserIdentificationOwn;
    }

    /**
     * @param SbrUserIdentificationOwn $sbrUserIdentificationOwn
     *
     * @return SbrCreationPosDetails
     */
    public function setSbrUserIdentificationOwn(SbrUserIdentificationOwn $sbrUserIdentificationOwn) : SbrCreationPosDetails
    {
        $this->sbrUserIdentificationOwn = $sbrUserIdentificationOwn;
        return $this;
    }

    /**
     * @return SbrPreferences
     */
    public function getSbrPreferences() : ?SbrPreferences
    {
        return $this->sbrPreferences;
    }

    /**
     * @param SbrPreferences $sbrPreferences
     *
     * @return SbrCreationPosDetails
     */
    public function setSbrPreferences(SbrPreferences $sbrPreferences) : SbrCreationPosDetails
    {
        $this->sbrPreferences = $sbrPreferences;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return SbrCreationPosDetails
     */
    public function populate(\stdClass $data) : SbrCreationPosDetails
    {
        if (isset($data->sbrUserIdentificationOwn)) {
            $this->sbrUserIdentificationOwn->populate($data->{'sbrUserIdentificationOwn'});
        }
        if (isset($data->sbrSystemDetails)) {
            $this->sbrSystemDetails->populate($data->{'sbrSystemDetails'});
        }
        if (isset($data->sbrPreferences)) {
            $this->sbrPreferences->populate($data->{'sbrPreferences'});
        }

        return $this;
    }
}
