<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Itinerary QueueingInformation Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class QueueingInformation extends AbstractModel
{
    /**
     * @var string
     */
    private $queueingOfficeId;

    /**
     * @return string
     */
    public function getQueueingOfficeId() : ?string
    {
        return $this->queueingOfficeId;
    }

    /**
     * @param string $queueingOfficeId
     *
     * @return QueueingInformation
     */
    public function setQueueingOfficeId(string $queueingOfficeId) : QueueingInformation
    {
        $this->queueingOfficeId = $queueingOfficeId;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return QueueingInformation
     */
    public function populate(\stdClass $data) : QueueingInformation
    {
        $this->queueingOfficeId= $data->queueingOfficeId ?? null;

        return $this;
    }
}