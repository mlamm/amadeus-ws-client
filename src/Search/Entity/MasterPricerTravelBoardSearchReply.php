<?php
namespace AmadeusService\Search\Entity;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class MasterPricesTravelBoardSearch
 * @package AmadeusService\Search\Entity
 * @see https://webservices.amadeus.com/extranet/structures/viewMessageStructure.do?id=5770&serviceVersionId=4507&isQuery=false#
 */
class MasterPricerTravelBoardSearchReply
{
    /**
     * Fare_MasterPricerTravelBoardSearchReply/replyStatus
     *
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ReplyStatus\Status>")
     * @XmlList(entry="status")
     * @SerializedName("replyStatus")
     * @var ArrayCollection
     */
    protected $replyStatus;

    /**
     * Fare_MasterPricerTravelBoardSearchReply/conversionRate
     *
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ConversionRate\ConversionRateDetail>")
     * @XmlList(entry="conversionRateDetail")
     * @SerializedName("conversionRate")
     * @var ArrayCollection
     */
    protected $conversionRate;

    /**
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex>")
     * @XmlList(entry="flightIndex", inline=true)
     * @SerializedName("flightIndex")
     * @var ArrayCollection
     */
    protected $flightIndex;

    /**
     * @return ArrayCollection
     */
    public function getReplyStatus()
    {
        return $this->replyStatus;
    }

    /**
     * @return ArrayCollection
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }
}