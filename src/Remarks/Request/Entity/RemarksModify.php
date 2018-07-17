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
    private $recordLocator;

    /**
     * @var ArrayCollection remarks to delete
     */
    private $remarks;

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
     *
     * @return RemarksModify
     */
    public function setRecordLocator($recordLocator) : RemarksModify
    {
        $this->recordLocator = $recordLocator;
        return $this;
    }

    /**
     * setter for remarks
     *
     * @param ArrayCollection $remarks
     *
     * @return RemarksModify
     */
    public function setRemarks(ArrayCollection $remarks) : RemarksModify
    {
        $this->remarks = $remarks;
        return $this;
    }

    /**
     * getter for remarks
     *
     * @return ArrayCollection
     */
    public function getRemarks() : ArrayCollection
    {
        return $this->remarks;
    }
}
