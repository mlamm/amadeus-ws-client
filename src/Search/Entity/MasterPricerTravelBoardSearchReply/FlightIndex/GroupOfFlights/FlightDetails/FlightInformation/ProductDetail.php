<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;

/**
 * Class ProductDetail
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation
 */
class ProductDetail
{
    /**
     * UN/IATA code identifying type of aircraft (747,737,...)
     * @SerializedName("equipmentType")
     * @Type("string")
     * @var string
     */
    protected $equipmentType;

    /**
     * Day number of the week (1,2,...,7)
     * @SerializedName("operatingDay")
     * @Type("string")
     * @var string
     */
    protected $operatingDay;

    /**
     * Number of stops enroute made in a journey if different from zero
     * @SerializedName("techStopNumber")
     * @Type("string")
     * @var string
     */
    protected $techStopNumber;

    /**
     * Location of stops
     * @SerializedName("locationId")
     * @Type("array<string>")
     * @XmlList(entry="locationId",inline=true)
     * @var array
     */
    protected $locationId;
}