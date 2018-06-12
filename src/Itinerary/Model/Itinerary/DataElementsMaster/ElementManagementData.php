<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo\Reference;

/**
 * ElementManagementData Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ElementManagementData extends AbstractModel
{
    /**
     * @var Reference
     */
    private $reference;

    /**
     * @var string
     */
    private $segmentName;

    /**
     * @var string
     */
    private $lineNumber;

    /**
     * ElementManagementData constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->reference = new Reference();

        parent::__construct($data);
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
     * @return ElementManagementData
     */
    public function setReference(Reference $reference) : ElementManagementData
    {
        $this->reference = $reference;
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
     * @return ElementManagementData
     */
    public function setSegmentName(string $segmentName) : ElementManagementData
    {
        $this->segmentName = $segmentName;
        return $this;
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
     * @return ElementManagementData
     */
    public function setLineNumber(string $lineNumber) : ElementManagementData
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->segmentName = $data->{'segmentName'} ?? null;
        $this->lineNumber  = $data->{'lineNumber'} ?? null;

        if (isset($data->reference)) {
            $this->reference->populate($data->{'reference'});
        }
    }
}
