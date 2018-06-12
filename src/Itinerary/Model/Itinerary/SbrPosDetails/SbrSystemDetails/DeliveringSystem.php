<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrSystemDetails;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Class Description
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class DeliveringSystem extends AbstractModel
{
    /**
     * @var string
     */
    private $companyId;

    /**
     * @var string
     */
    private $locationId;

    /**
     * @param \stdClass $data
     *
     * @return DeliveringSystem
     */
    public function populate(\stdClass $data) : DeliveringSystem
    {
        $this->companyId  = $data->{'companyId'} ?? null;
        $this->locationId = $data->{'locationId'} ?? null;

        return $this;
    }
}
