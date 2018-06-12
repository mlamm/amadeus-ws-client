<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Selection Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SelectionDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $selection;

    /**
     * @return string
     */
    public function getSelection() : ?string
    {
        return $this->selection;
    }

    /**
     * @param string $selection
     *
     * @return SelectionDetails
     */
    public function setSelection(string $selection) : SelectionDetails
    {
        $this->selection = $selection;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->selection = $data->{'selection'} ?? null;
    }
}
