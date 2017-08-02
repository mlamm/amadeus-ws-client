<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class FlightProposal
 * Fare_MasterPricerTravelBoardSearchReply/flightIndex/groupOfFlights/propFlightGrDetail/flightProposal
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\GroupOfFlights\PropFlightGrDetail
 */
class FlightProposal
{
    /**
     * To specify quality weight of a suggested recommendation, Elapsed Flying Time, Proposed Segment Number or Availibility Segment Number, ...
     * @Type("string")
     * @SerializedName("ref")
     * @var string
     */
    protected $ref;

    /**
     * To determine Number of seats, User Quality, Customer Type, Number of Best Buy, ...
     * @Type("string")
     * @SerializedName("unitQualifier")
     * @var string
     */
    protected $unitQualifier;
}