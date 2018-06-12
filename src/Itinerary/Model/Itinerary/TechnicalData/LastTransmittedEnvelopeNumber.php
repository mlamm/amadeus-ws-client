<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * LastTransmittedEnvelopeNumber Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class LastTransmittedEnvelopeNumber extends AbstractModel
{
    /**
     * @var string
     */
    private $currentRecord;

    /**
     * @return string
     */
    public function getCurrentRecord() : ?string
    {
        return $this->currentRecord;
    }

    /**
     * @param string $currentRecord
     *
     * @return LastTransmittedEnvelopeNumber
     */
    public function setCurrentRecord(string $currentRecord) : LastTransmittedEnvelopeNumber
    {
        $this->currentRecord = $currentRecord;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
     */
    public function populate(\stdClass $data)
    {
        $this->currentRecord = $data->{'currentRecord'} ?? null;
    }
}
