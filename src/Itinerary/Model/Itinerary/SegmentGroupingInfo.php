<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SegmentGroupingInfo\MarriageDetail;

/**
 * SegmentGroupingInfo Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SegmentGroupingInfo extends AbstractModel
{
    /**
     * @var string
     */
    private $groupingCode;

    /**
     * @var ArrayCollection
     */
    private $marriageDetail;

    /**
     * SegmentGroupingInfo constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->marriageDetail = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getGroupingCode() : ?string
    {
        return $this->groupingCode;
    }

    /**
     * @param string $groupingCode
     *
     * @return SegmentGroupingInfo
     */
    public function setGroupingCode(string $groupingCode) : SegmentGroupingInfo
    {
        $this->groupingCode = $groupingCode;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMarriageDetail() : ?ArrayCollection
    {
        return $this->marriageDetail;
    }

    /**
     * @param MarriageDetail $marriageDetail
     *
     * @return SegmentGroupingInfo
     */
    public function addMarriageDetail(MarriageDetail $marriageDetail) : SegmentGroupingInfo
    {
        $this->marriageDetail->add($marriageDetail);
        return $this;
    }

    /**
     * @param ArrayCollection $marriageDetail
     *
     * @return SegmentGroupingInfo
     */
    public function setMarriageDetail(ArrayCollection $marriageDetail) : SegmentGroupingInfo
    {
        $this->marriageDetail = $marriageDetail;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->groupingCode = $data->{'groupingCode'} ?? null;
        $this->mapMarriageDetails($data->{'marriageDetail'});
    }

    /**
     * @param $data
     */
    private function mapMarriageDetails($data)
    {
        if (is_array($data)) {
            foreach ($data as $detail) {
                $marriageDetail = new MarriageDetail();
                $marriageDetail->populate($detail);
                $this->addMarriageDetail($marriageDetail);
            }
        } else {
            $marriageDetail = new MarriageDetail();
            $marriageDetail->populate($data);
            $this->addMarriageDetail($marriageDetail);
        }
    }
}
