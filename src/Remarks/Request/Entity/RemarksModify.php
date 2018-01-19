<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * entity for remarksModify request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class RemarksModify
{
    /**
     * @var string identification of pnr
     */
    private $recordlocator;

    /**
     * @var ArrayCollection remarks to delete
     */
    private $remarks;

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
     * @return RemarksModify
     */
    public function setRecordlocator($recordlocator) : RemarksModify
    {
        $this->recordlocator = $recordlocator;
        return $this;
    }

    public function setRemarks(ArrayCollection $remarks) : RemarksModify
    {
        $this->remarks = $remarks;
        return $this;
    }

    public function getRemarks() : ArrayCollection
    {
        return $this->remarks;
    }
}