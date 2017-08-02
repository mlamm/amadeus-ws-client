<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class Location
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/flightDetails/flightInformation/location
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation
 */
class Location
{
    /**
     * @SerializedName("locationId")
     * @Type("string")
     * @var string
     */
    protected $locationId;

    /**
     * @SerializedName("airportCityQualifier")
     * @Type("string")
     * @var string
     */
    protected $airportCityQualifier;

    /**
     * @SerializedName("terminal")
     * @Type("string")
     * @var string
     */
    protected $terminal;
}