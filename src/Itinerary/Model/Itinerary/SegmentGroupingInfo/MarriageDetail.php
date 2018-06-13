<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SegmentGroupingInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * MarriageDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class MarriageDetail extends AbstractModel
{
    /**
     * @var string
     */
    private $marriageQualifier;

    /**
     * @var string
     */
    private $tatooNum;

    /**
     * @return string
     */
    public function getTatooNum() : ?string
    {
        return $this->tatooNum;
    }

    /**
     * @param string $tatooNum
     *
     * @return MarriageDetail
     */
    public function setTatooNum(string $tatooNum) : MarriageDetail
    {
        $this->tatooNum = $tatooNum;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarriageQualifier() : ?string
    {
        return $this->marriageQualifier;
    }

    /**
     * @param string $marriageQualifier
     *
     * @return MarriageDetail
     */
    public function setMarriageQualifier(string $marriageQualifier) : MarriageDetail
    {
        $this->marriageQualifier = $marriageQualifier;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->marriageQualifier= $data->marriageQualifier ?? null;
        $this->tatooNum         = $data->tatooNum ?? null;
    }
}
