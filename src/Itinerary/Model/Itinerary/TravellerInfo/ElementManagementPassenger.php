<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\TravellerInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * ElementManagementPassenger Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ElementManagementPassenger extends AbstractModel
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
     * ElementManagementPassenger constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
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
     * @return ElementManagementPassenger
     */
    public function setReference(Reference $reference) : ElementManagementPassenger
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
     * @return ElementManagementPassenger
     */
    public function setSegmentName(string $segmentName) : ElementManagementPassenger
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
     * @return ElementManagementPassenger
     */
    public function setLineNumber(string $lineNumber) : ElementManagementPassenger
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return mixed|void
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
