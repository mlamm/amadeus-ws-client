<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * OffpointDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class OffpointDetail extends AbstractModel
{
    /**
     * @var string
     */
    private $cityCode;

    /**
     * @return string
     */
    public function getCityCode() : ?string
    {
        return $this->cityCode;
    }

    /**
     * @param string $cityCode
     *
     * @return OffpointDetail
     */
    public function setCityCode(string $cityCode) : OffpointDetail
    {
        $this->cityCode = $cityCode;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->cityCode= $data->cityCode ?? null;
    }
}
