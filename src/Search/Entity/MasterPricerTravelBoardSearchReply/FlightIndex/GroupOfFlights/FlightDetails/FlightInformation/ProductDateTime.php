<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class ProductDateTime
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/flightDetails/flightInformation/productDateTime
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation
 */
class ProductDateTime
{
    /**
     * A date that applies to a means of transport or a traveller.
     * @SerializedName("dateOfDeparture")
     * @Type("string")
     * @var string
     */
    protected $dateOfDeparture;

    /**
     * @SerializedName("timeOfDeparture")
     * @Type("string")
     * @var string
     */
    protected $timeOfDeparture;

    /**
     * @SerializedName("dateOfArrival")
     * @Type("string")
     * @var string
     */
    protected $dateOfArrival;

    /**
     * @SerializedName("timeOfArrival")
     * @Type("string")
     * @var string
     */
    protected $timeOfArrival;

    /**
     * A number to indicate the difference between first date and second date due to time zones.
     * @SerializedName("dateVariation")
     * @Type("string")
     * @var string
     */
    protected $dateVariation;
}