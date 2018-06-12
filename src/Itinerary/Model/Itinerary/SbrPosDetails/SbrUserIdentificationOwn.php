<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrUserIdentificationOwn\OriginIdentification;

/**
 * SbrUserIdentificationOwn Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrUserIdentificationOwn extends AbstractModel
{
    /**
     * @var OriginIdentification
     */
    private $originIdentification;

    /**
     * @var string
     */
    private $originatorTypeCode;

    /**
     * SbrUserIdentificationOwn constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->originIdentification = new OriginIdentification();

        parent::__construct($data);
    }

    /**
     * @return OriginIdentification
     */
    public function getOriginIdentification() : ?OriginIdentification
    {
        return $this->originIdentification;
    }

    /**
     * @param OriginIdentification $originIdentification
     *
     * @return SbrUserIdentificationOwn
     */
    public function setOriginIdentification(OriginIdentification $originIdentification) : SbrUserIdentificationOwn
    {
        $this->originIdentification = $originIdentification;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginatorTypeCode() : ?string
    {
        return $this->originatorTypeCode;
    }

    /**
     * @param string $originatorTypeCode
     *
     * @return SbrUserIdentificationOwn
     */
    public function setOriginatorTypeCode(string $originatorTypeCode) : SbrUserIdentificationOwn
    {
        $this->originatorTypeCode = $originatorTypeCode;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return $this
     */
    public function populate(\stdClass $data) : SbrUserIdentificationOwn
    {
        $this->originatorTypeCode   = $data->{'originatorTypeCode'} ?? null;

        if (isset($data->originatorTypeCode)) {
            $this->originIdentification->populate($data->{'originIdentification'});
        }
        return $this;
    }
}