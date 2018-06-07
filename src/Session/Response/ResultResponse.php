<?php

namespace Flight\Service\Amadeus\Session\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Application\Response\HalResponse;

/**
 * ResultResponse
 *
 * Description of the class
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ResultResponse extends HalResponse
{
    /**
     * @var ArrayCollection
     */
    protected $result;

    /**
     * @return ArrayCollection
     */
    public function getResult(): ArrayCollection
    {
        return $this->result;
    }

    /**
     * @param ArrayCollection $result
     * @return ResultResponse
     */
    public function setResult(ArrayCollection $result): ResultResponse
    {
        $this->result = $result;
        return $this;
    }
}