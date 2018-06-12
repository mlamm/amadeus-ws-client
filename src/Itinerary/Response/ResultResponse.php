<?php

namespace Flight\Service\Amadeus\Itinerary\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Application\Response\HalResponse;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary;

/**
 * Class ItineraryResultResponse
 *
 * @package Flight\Service\Amadeus\Itinerary\Response
 */
class ResultResponse extends HalResponse
{
    /**
     * @var Itinerary
     */
    protected $result;

    /**
     * @return Itinerary
     */
    public function getResult() : Itinerary
    {
        return $this->result;
    }

    /**
     * @param Itinerary $result
     *
     * @return ResultResponse
     */
    public function setResult(Itinerary $result) : ResultResponse
    {
        $this->result = $result;
        return $this;
    }

}
