<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

/**
 * CabinClass.php
 *
 * Convert the cabin class info
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ManagementData
{

    /**
     * name of the segment
     * @var string
     */
    private $segmentName;

    /**
     * line number
     *
     * @var integer
     */
    private $lineNumber;

    /**
     * reference
     *
     * @var Reference
     */
    private $reference;

    /**
     * getter for segmentName
     *
     * @return string
     */
    public function getSegmentName()
    {
        return $this->segmentName;
    }

    /**
     * setter for segmentName
     *
     * @param string $segmentName
     * @return ManagementData
     */
    public function setSegmentName($segmentName)
    {
        $this->segmentName = $segmentName;
        return $this;
    }

    /**
     * getter for lineNumber
     *
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * setter for lineNumber
     *
     * @param int $lineNumber
     * @return ManagementData
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * getter for reference
     *
     * @return Reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * setter for reference
     *
     * @param Reference $reference
     * @return ManagementData
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

}
