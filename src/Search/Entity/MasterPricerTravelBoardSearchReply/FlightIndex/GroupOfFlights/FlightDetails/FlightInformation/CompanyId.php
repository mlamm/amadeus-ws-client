<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class CompanyId
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation
 */
class CompanyId
{
    /**
     * Marketing carrier
     * @SerializedName("marketingCarrier")
     * @Type("string")
     * @var string
     */
    protected $marketingCarrier;

    /**
     * Operating carrier
     * @SerializedName("operatingCarrier")
     * @Type("string")
     * @var string
     */
    protected $operatingCarrier;

    /**
     * Operating carrier
     * @SerializedName("alliance")
     * @Type("string")
     * @var string
     */
    protected $alliance;
}