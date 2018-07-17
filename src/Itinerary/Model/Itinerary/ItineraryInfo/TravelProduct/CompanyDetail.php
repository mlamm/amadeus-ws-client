<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * CompanyDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class CompanyDetail extends AbstractModel
{
    /**
     * @var string
     */
    private $identification;

    /**
     * @return string
     */
    public function getIdentification() : ?string
    {
        return $this->identification;
    }

    /**
     * @param string $identification
     *
     * @return CompanyDetail
     */
    public function setIdentification(string $identification) : CompanyDetail
    {
        $this->identification = $identification;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->identification= $data->identification ?? null;
    }
}
