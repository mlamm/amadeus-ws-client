<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\FlightIndex\RequestedSegmentRef;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class LocationForcing
{
    /**
     * Airport/City Qualifier: the passenger wants to depart / arrive from / to the same airport or city as in the specified Requested Segment
     * @Type("string")
     * @SerializedName("airportCityQualifier")
     * @var string
     */
    protected $airportCityQualifier;

    /**
     * Specifies a reference given to a number of flight segment in the querying system
     * @Type("string")
     * @SerializedName("segmentNumber")
     * @var string
     */
    protected $segmentNumber;
}