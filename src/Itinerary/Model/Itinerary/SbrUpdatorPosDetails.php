<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrPreferences;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrSystemDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrUserIdentificationOwn;

/**
 * SbrUpdatorPosDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrUpdatorPosDetails extends AbstractModel
{
    /**
     * @var SbrUserIdentificationOwn
     */
    private $sbrUserIdentificationOwn;

    /**
     * @var SbrSystemDetails
     */
    private $sbrSystemDetails;

    /**
     * @var SbrPreferences
     */
    private $sbrPreferences;

    /**
     * SbrUpdatorPosDetails constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->sbrUserIdentificationOwn = new SbrUserIdentificationOwn();
        $this->sbrPreferences           = new SbrPreferences();
        $this->sbrSystemDetails         = new SbrSystemDetails();

        parent::__construct($data);
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
     * @return SbrUpdatorPosDetails
     */
    public function setSbrUserIdentificationOwn(SbrUserIdentificationOwn $sbrUserIdentificationOwn) : SbrUpdatorPosDetails
    {
        $this->sbrUserIdentificationOwn = $sbrUserIdentificationOwn;
        return $this;
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
     * @return SbrUpdatorPosDetails
     */
    public function setSbrSystemDetails(SbrSystemDetails $sbrSystemDetails) : SbrUpdatorPosDetails
    {
        $this->sbrSystemDetails = $sbrSystemDetails;
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
     * @return SbrUpdatorPosDetails
     */
    public function setSbrPreferences(SbrPreferences $sbrPreferences) : SbrUpdatorPosDetails
    {
        $this->sbrPreferences = $sbrPreferences;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
     */
    public function populate(\stdClass $data)
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
    }
}
