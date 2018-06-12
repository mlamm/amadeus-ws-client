<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * ItineraryMessageAction Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryMessageAction extends AbstractModel
{
    /**
     * @var string
     */
    private $business;

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->business) && isset($data->{'business'}->function)) {
            $this->business = $data->{'business'}->{'function'};
        }
    }
}
