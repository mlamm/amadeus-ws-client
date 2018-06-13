<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo\Reference;

/**
 * ElementManagementItinerary Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ElementManagementItinerary extends AbstractModel
{
    /**
     * @var string
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $segmentName;

    /**
     * @var Reference
     */
    private $reference;

    /**
     * ElementManagementItinerary constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->reference = new Reference();

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getLineNumber() : ?string
    {
        return $this->lineNumber;
    }

    /**
     * @param string $lineNumber
     *
     * @return ElementManagementItinerary
     */
    public function setLineNumber(string $lineNumber) : ElementManagementItinerary
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getSegmentName() : ?string
    {
        return $this->segmentName;
    }

    /**
     * @param string $segmentName
     *
     * @return ElementManagementItinerary
     */
    public function setSegmentName(string $segmentName) : ElementManagementItinerary
    {
        $this->segmentName = $segmentName;
        return $this;
    }

    /**
     * @return Reference
     */
    public function getReference() : ?Reference
    {
        return $this->reference;
    }

    /**
     * @param Reference $reference
     *
     * @return ElementManagementItinerary
     */
    public function setReference(Reference $reference) : ElementManagementItinerary
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->segmentName= $data->segmentName ?? null;
        $this->lineNumber = $data->lineNumber ?? null;

        if (isset($data->reference)) {
            $this->reference->populate($data->{'reference'});
        }
    }
}
