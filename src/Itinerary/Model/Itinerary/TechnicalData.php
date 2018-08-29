<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData\EnveloppeNumberData;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData\LastTransmittedEnvelopeNumber;

/**
 * TechnicalData Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class TechnicalData extends AbstractModel
{
    /**
     * @var EnveloppeNumberData
     */
    private $enveloppeNumberData;

    /**
     * @var LastTransmittedEnvelopeNumber
     */
    private $lastTransmittedEnvelopeNumber;

    /**
     * @var \DateTime
     */
    private $purgeDateData;

    /**
     * TechnicalData constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->enveloppeNumberData           = new EnveloppeNumberData();
        $this->lastTransmittedEnvelopeNumber = new LastTransmittedEnvelopeNumber();
        $this->purgeDateData                 = new \DateTime();

        parent::__construct($data);
    }

    /**
     * @return EnveloppeNumberData
     */
    public function getEnveloppeNumberData() : ?EnveloppeNumberData
    {
        return $this->enveloppeNumberData;
    }

    /**
     * @param EnveloppeNumberData $enveloppeNumberData
     *
     * @return TechnicalData
     */
    public function setEnveloppeNumberData(EnveloppeNumberData $enveloppeNumberData) : TechnicalData
    {
        $this->enveloppeNumberData = $enveloppeNumberData;
        return $this;
    }

    /**
     * @return LastTransmittedEnvelopeNumber
     */
    public function getLastTransmittedEnvelopeNumber() : ?LastTransmittedEnvelopeNumber
    {
        return $this->lastTransmittedEnvelopeNumber;
    }

    /**
     * @param LastTransmittedEnvelopeNumber $lastTransmittedEnvelopeNumber
     *
     * @return TechnicalData
     */
    public function setLastTransmittedEnvelopeNumber(LastTransmittedEnvelopeNumber $lastTransmittedEnvelopeNumber) : TechnicalData
    {
        $this->lastTransmittedEnvelopeNumber = $lastTransmittedEnvelopeNumber;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPurgeDateData() : ?\DateTime
    {
        return $this->purgeDateData;
    }

    /**
     * @param \DateTime $purgeDateData
     *
     * @return TechnicalData
     */
    public function setPurgeDateData(\DateTime $purgeDateData) : TechnicalData
    {
        $this->purgeDateData = $purgeDateData;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->{'purgeDateData'}, $data->{'purgeDateData'}->{'dateTime'})) {
            $date = $data->purgeDateData->{'dateTime'};
            $year  = $date->{'year'} ?? date('Y');
            $month = $date->{'month'} ?? date('m');
            $day   = $date->{'day'} ?? date('d');
            $this->purgeDateData->setDate($year, $month, $day);
            $this->purgeDateData->setTime(0, 0);
        }
        if (isset($data->enveloppeNumberData)) {
            $this->enveloppeNumberData->populate($data->{'enveloppeNumberData'});
        }
        if (isset($data->lastTransmittedEnvelopeNumber)) {
            $this->lastTransmittedEnvelopeNumber->populate($data->{'lastTransmittedEnvelopeNumber'});
        }
    }
}
