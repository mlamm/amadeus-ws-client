<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\RequestedSegmentRef;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\XmlRoot;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights;

/**
 * Class FlightIndex
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply
 * @XmlRoot("flightIndex")
 */
class FlightIndex
{
    /**
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\RequestedSegmentRef>")
     * @XmlList(entry="requestedSegmentRef", inline=true)
     * @SerializedName("requestedSegmentRef")
     * @var ArrayCollection
     */
    protected $requestedSegmentRef;

    /**
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights>")
     * @XmlList(entry="groupOfFlights", inline=true)
     * @SerializedNAme("groupOfFlights")
     * @var ArrayCollection
     */
    protected $groupOfFlights;
}