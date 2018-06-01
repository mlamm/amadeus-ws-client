<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Itinerary\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * model for itinerary
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Itinerary
{

    /**
     * remarks of the itinerary
     *
     * @var ArrayCollection|Remark[]
     */
    private $remarks;

    /**
     * getter for remarks
     *
     * @return ArrayCollection|Remark[]
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * setter for remarks
     *
     * @param ArrayCollection|Remark[] $remarks
     * @return Itinerary
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
        return $this;
    }
}
