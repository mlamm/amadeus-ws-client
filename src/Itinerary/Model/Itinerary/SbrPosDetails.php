<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrUserIdentificationOwn;

/**
 * SbrPOSDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrPosDetails extends AbstractModel
{
    /**
     * @var SbrUserIdentificationOwn
     */
    private $sbrUserIdentificationOwn;

    /**
     * SbrPosDetails constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->sbrUserIdentificationOwn = new SbrUserIdentificationOwn();
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
     * @return SbrPosDetails
     */
    public function setSbrUserIdentificationOwn(SbrUserIdentificationOwn $sbrUserIdentificationOwn) : SbrPosDetails
    {
        $this->sbrUserIdentificationOwn = $sbrUserIdentificationOwn;
        return $this;
    }

    /**
     * populate data from an stdClass
     *
     * @param \stdClass $data
     *
     * @return SbrPosDetails
     */
    public function populate(\stdClass $data) : SbrPosDetails
    {
        if (isset($data->sbrUserIdentificationOwn)) {
            $this->sbrUserIdentificationOwn->populate($data->{'sbrUserIdentificationOwn'});
        }

        return $this;
    }
}
