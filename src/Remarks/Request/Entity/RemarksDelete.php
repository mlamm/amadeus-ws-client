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
     * @return RemarksDelete
     */
    public function setRecordlocator($recordlocator) : RemarksDelete
    {
        $this->recordlocator = $recordlocator;
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