<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

/**
 * entity for remarksRead request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class RemarksDelete
{
    /**
     * @var string identification of pnr
     */
    private $recordlocator;

    /**
     * getter for recordlocator
     *
     * @return string
     */
    public function getRecordlocator() : string
    {
        return $this->recordlocator;
    }

    /**
     * setter for recordlocator
     *
     * @param string $recordlocator
     * @return RemarksDelete
     */
    public function setRecordlocator($recordlocator) : RemarksRead
    {
        $this->recordlocator = $recordlocator;
        return $this;
    }
}