<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * Class FlightDetails
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/flightDetails
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights
 */
class FlightDetails
{
    /**
     * @Type("AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation")
     * @SerializedName("flightInformation")
     * @var FlightInformation
     */
    protected $flightInformation;
}