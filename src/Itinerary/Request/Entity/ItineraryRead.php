<?php

namespace Flight\Service\Amadeus\Itinerary\Request\Entity;

/**
 * ItineraryRead entity
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryRead
{
    /**
     * @var string
     */
    private $recordLocator;

    /**
     * ItineraryRead constructor.
     *
     * @param null $recordLocator
     */
    public function __construct($recordLocator = null)
    {
        $this->recordLocator = $recordLocator;
    }

    /**
     * @return string
     */
    public function getRecordLocator() : string
    {
        return $this->recordLocator;
    }

    /**
     * @param string $recordLocator
     *
     * @return ItineraryRead
     */
    public function setRecordLocator(string $recordLocator) : ItineraryRead
    {
        $this->recordLocator = $recordLocator;
        return $this;
    }
}
