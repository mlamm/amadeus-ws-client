<?php

namespace Flight\Service\Amadeus\Session\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Session\Response\ResultResponse;

/**
 * AmadeusResponseTransformer
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusResponseTransformer
{
    /**
     * @param Result $result
     * @return ResultResponse
     */
    public function mapSessionCreate(Result $result): ResultResponse
    {

    }
}