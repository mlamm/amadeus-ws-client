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
    private $recordlocator;

    /**
     * @var ArrayCollection remarks to add
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
     * @return RemarksAdd
     */
    public function setRecordlocator($recordlocator) : RemarksAdd
    {
        $this->recordlocator = $recordlocator;
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