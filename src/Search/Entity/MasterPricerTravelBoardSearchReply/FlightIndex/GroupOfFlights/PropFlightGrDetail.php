<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlList;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail\FlightProposal;

/**
 * Class PropFlightGrDetail
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/propFlightGrDetail
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights
 */
class PropFlightGrDetail
{
    /**
     * Parameters for proposed flight group
     * @Type("ArrayCollection<AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail\FlightProposal>")
     * @SerializedName("flightProposal")
     * @XmlList(entry="flightProposal",inline=true)
     * @var ArrayCollection
     */
    protected $flightProposal;

    /**
     * Provides some Details at flight level
     * @Type("string")
     * @SerializedName("flightCharacteristic")
     * @var string
     */
    protected $flightCharacteristic;

    /**
     * Designates the class of service on the means of transport in which the passenger will travel.
     * @Type("string")
     * @SerializedName("majCabin")
     * @var string
     */
    protected $majCabin;
}