<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * TypeDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class TypeDetail extends AbstractModel
{
    /**
     * @var string
     */
    private $detail;

    /**
     * @var string
     */
    private $processingIndicator;

    /**
     * @return string
     */
    public function getDetail() : ?string
    {
        return $this->detail;
    }

    /**
     * @param string $detail
     *
     * @return TypeDetail
     */
    public function setDetail(string $detail) : TypeDetail
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingIndicator() : ?string
    {
        return $this->processingIndicator;
    }

    /**
     * @param string $processingIndicator
     *
     * @return TypeDetail
     */
    public function setProcessingIndicator(string $processingIndicator) : TypeDetail
    {
        $this->processingIndicator = $processingIndicator;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->detail              = $data->{'detail'} ?? null;
        $this->processingIndicator = $data->{'processingIndicator'} ?? null;
    }
}
