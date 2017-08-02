<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\RequestedSegmentRef\LocationForcing;

/**
 * Class RequestedSegmentRef
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/requestedSegmentRef
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex
 */
class RequestedSegmentRef
{
    /**
     * 1-3 numerics to specify percentage of open classes, quality weight of a suggested recommendation, number of passengers, days or number of Best Buy
     * @Type("string")
     * @SerializedName("segRef")
     * @var string
     */
    protected $segRef;

    /**
     * Forces arrival or departure, from/to the same airport/city
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\RequestedSegmentRef\LocationForcing>")
     * @SerializedName("locationForcing")
     * @XmlList(entry="locationForcing",inline=true)
     * @var ArrayCollection
     */
    protected $locationForcing;
}