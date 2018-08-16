<?php

namespace Flight\Service\Amadeus\Price\Response;

use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Price\Model\Price;

/**
 * Response for creating pricing.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class PriceGetResponse extends HalResponse
{
    /**
     * @var Price
     */
    protected $result;

    /**
     * @return Price
     */
    public function getResult() : Price
    {
        return $this->result;
    }

    /**
     * @param Price $result
     *
     * @return PriceGetResponse
     */
    public function setResult(Price $result) : PriceGetResponse
    {
        $this->result = $result;
        return $this;
    }
}
