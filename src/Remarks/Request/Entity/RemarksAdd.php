<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * entity for remarksAdd request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class RemarksAdd
{
    /**
     * @var string identification of pnr
     */
    private $recordLocator;

    /**
     * @var ArrayCollection remarks to add
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
     * @return RemarksAdd
     */
    public function setRecordLocator($recordLocator) : RemarksAdd
    {
        $this->recordLocator = $recordLocator;
        return $this;
    }

    /**
     * setter for remarks
     *
     * @param ArrayCollection $remarks
     *
     * @return RemarksAdd
     */
    public function setRemarks(ArrayCollection $remarks) : RemarksAdd
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