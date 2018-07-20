<?php
namespace Flight\Service\Amadeus\Session\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Application\Response\HalResponse;

/**
 * Class SearchResultResponse
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class SessionCreateResponse extends HalResponse
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
     * @return SessionCreateResponse
     */
    public function setResult(ArrayCollection $result): SessionCreateResponse
    {
        $this->result = $result;
        return $this;
    }
}
