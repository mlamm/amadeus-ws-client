<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrSystemDetails\DeliveringSystem;

/**
 * SbrSystemDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrSystemDetails extends AbstractModel
{
    /**
     * @var DeliveringSystem
     */
    private $deliveringSystem;

    /**
     * @var SbrUserIdentificationOwn
     */
    private $sbrUserIdentificationOwn;

    /**
     * @var SbrPreferences
     */
    private $sbrPreferences;

    /**
     * SbrSystemDetails constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->sbrPreferences           = new SbrPreferences();
        $this->deliveringSystem         = new DeliveringSystem();
        $this->sbrUserIdentificationOwn = new SbrUserIdentificationOwn();

        parent::__construct($data);
    }

    /**
     * @return DeliveringSystem
     */
    public function getDeliveringSystem() : ?DeliveringSystem
    {
        return $this->deliveringSystem;
    }

    /**
     * @param DeliveringSystem $deliveringSystem
     *
     * @return SbrSystemDetails
     */
    public function setDeliveringSystem(DeliveringSystem $deliveringSystem) : SbrSystemDetails
    {
        $this->deliveringSystem = $deliveringSystem;
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
     * @return SbrSystemDetails
     */
    public function setSbrUserIdentificationOwn(SbrUserIdentificationOwn $sbrUserIdentificationOwn) : SbrSystemDetails
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
     * @return SbrSystemDetails
     */
    public function setSbrPreferences(SbrPreferences $sbrPreferences) : SbrSystemDetails
    {
        $this->sbrPreferences = $sbrPreferences;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->deliveringSystem)) {
            $this->deliveringSystem->populate($data->{'deliveringSystem'});
        }
        if (isset($data->sbrUserIdentificationOwn)) {
            $this->sbrUserIdentificationOwn->populate($data->{'sbrUserIdentificationOwn'});
        }
        if (isset($data->sbrPreferences)) {
            $this->sbrPreferences->populate($data->{'sbrPreferences'});
        }
    }
}