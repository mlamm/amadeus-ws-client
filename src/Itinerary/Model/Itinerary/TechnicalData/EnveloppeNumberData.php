<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * EnveloppeNumberData Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class EnveloppeNumberData extends AbstractModel
{
    /**
     * @var SequenceDetails
     */
    private $sequenceDetails;

    /**
     * EnveloppeNumberData constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->sequenceDetails = new SequenceDetails();

        parent::__construct($data);
    }

    /**
     * @return SequenceDetails
     */
    public function getSequenceDetails() : ?SequenceDetails
    {
        return $this->sequenceDetails;
    }

    /**
     * @param SequenceDetails $sequenceDetails
     *
     * @return EnveloppeNumberData
     */
    public function setSequenceDetails(SequenceDetails $sequenceDetails) : EnveloppeNumberData
    {
        $this->sequenceDetails = $sequenceDetails;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->sequenceDetails)) {
            $this->sequenceDetails->populate($data->{'sequenceDetails'});
        }
    }
}
