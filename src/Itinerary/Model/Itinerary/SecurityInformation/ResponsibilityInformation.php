<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Itinerary ResponsibilityInformation Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ResponsibilityInformation extends AbstractModel
{
    /**
     * @var string
     */
    private $typeOfPnrElement;

    /**
     * @var string
     */
    private $agentId;

    /**
     * @var string
     */
    private $officeId;

    /**
     * @return string
     */
    public function getTypeOfPnrElement() : ?string
    {
        return $this->typeOfPnrElement;
    }

    /**
     * @param string $typeOfPnrElement
     *
     * @return ResponsibilityInformation
     */
    public function setTypeOfPnrElement(string $typeOfPnrElement) : ResponsibilityInformation
    {
        $this->typeOfPnrElement = $typeOfPnrElement;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgentId() : ?string
    {
        return $this->agentId;
    }

    /**
     * @param string $agentId
     *
     * @return ResponsibilityInformation
     */
    public function setAgentId(string $agentId) : ResponsibilityInformation
    {
        $this->agentId = $agentId;
        return $this;
    }

    /**
     * @return string
     */
    public function getOfficeId() : ?string
    {
        return $this->officeId;
    }

    /**
     * @param string $officeId
     *
     * @return ResponsibilityInformation
     */
    public function setOfficeId(string $officeId) : ResponsibilityInformation
    {
        $this->officeId = $officeId;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return ResponsibilityInformation
     */
    public function populate(\stdClass $data) : ResponsibilityInformation
    {
        $this->typeOfPnrElement= $data->typeOfPnrElement ?? null;
        $this->officeId        = $data->officeId ?? null;
        $this->agentId         = $data->agentId ?? null;

        return $this;
    }
}
