<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\CompanyId;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\ProductDateTime;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\ProductDetail;
use Doctrine\Common\Collections\ArrayCollection;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\Location;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;

/**
 * Class FlightInformation
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/flightDetails/flightInformation
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails
 */
class FlightInformation
{
    /**
     * Date and time of departure and arrival
     * @Type("AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\ProductDateTime")
     * @SerializedName("productDateTime")
     * @var ProductDateTime
     */
    protected $productDateTime;

    /**
     * Location of departure and arrival
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\Location>")
     * @SerializedName("location")
     * @XmlList(entry="location",inline=true)
     * @var ArrayCollection
     */
    protected $location;

    /**
     * @Type("AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\CompanyId")
     * @SerializedName("companyId")
     * @var CompanyId
     */
    protected $companyId;

    /**
     * 1-4 digits for flight number
     * @SerializedName("flightOrtrainNumber")
     * @Type("string")
     * @var string
     */
    protected $flightOrtrainNumber;

    /**
     * Product details
     * @Type("AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\FlightDetails\FlightInformation\ProductDetail")
     * @SerializedName("productDetail")
     * @var ProductDetail
     */
    protected $productDetail;
}