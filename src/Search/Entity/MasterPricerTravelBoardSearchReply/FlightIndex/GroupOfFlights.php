<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails;

/**
 * Class GroupOfFlights
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex
 */
class GroupOfFlights
{
    /**
     * @Type("AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail")
     * @SerializedName("propFlightGrDetail")
     * @var PropFlightGrDetail\
     */
    protected $propFlightGrDetail;

    /**
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails>")
     * @SerializedName("flightDetails")
     * @XmlList(entry="flightDetails",inline=true)
     * @var ArrayCollection
     */
    protected $flightDetails;
}