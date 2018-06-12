<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * RelatedProduct Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class RelatedProduct extends AbstractModel
{
    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $status;

    /**
     * @return string
     */
    public function getQuantity() : ?string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     *
     * @return RelatedProduct
     */
    public function setQuantity(string $quantity) : RelatedProduct
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return RelatedProduct
     */
    public function setStatus(string $status) : RelatedProduct
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->quantity = $data->{'quantity'} ?? null;
        $this->status   = $data->{'status'} ?? null;
    }
}
