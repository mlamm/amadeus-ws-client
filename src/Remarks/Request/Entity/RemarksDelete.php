<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * entity for remarksDelete request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class RemarksDelete
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
     * @return RemarksDelete
     */
    public function setRecordLocator($recordLocator) : RemarksDelete
    {
        $this->recordLocator = $recordLocator;
        return $this;
    }
    /**
     * setter for remarks
     *
     * @param ArrayCollection $remarks
     *
     * @return RemarksDelete
     */
    public function setRemarks(ArrayCollection $remarks) : RemarksDelete
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