<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

/**
 * entity for remarksRead request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class RemarksRead
{
    /**
     * @var string identification of pnr
     */
    private $recordLocator;

    /**
     * getter for recordLocator
     *
     * @return string
     */
    public function getRecordLocator() : string
    {
        return $this->recordLocator;
    }

    /**
     * setter for recordLocator
     *
     * @param string $recordLocator
     * @return RemarksRead
     */
    public function setRecordLocator($recordLocator) : RemarksRead
    {
        $this->recordLocator = $recordLocator;
        return $this;
    }
}