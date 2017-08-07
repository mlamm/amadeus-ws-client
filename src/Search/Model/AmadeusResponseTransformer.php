<?php
namespace AmadeusService\Search\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;

/**
 * Class AmadeusResponseTransformer
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseTransformer
{
    /**
     * @var Result
     */
    protected $amadeusResult;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var SearchResponse
     */
    private $mappedResponse;

    /**
     * AmadeusResponseTransformer constructor.
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->amadeusResult = $result;
        $this->mapper = new Mapper(getcwd() . '/var/cache/response-mapping/');
        $this->mapResultToDefinedStructure();
    }


    private function mapResultToDefinedStructure()
    {
        /** @var SearchResponse $searchResponse */
        $searchResponse = new SearchResponse();
        $searchResponse->setResult(new ArrayCollection());

        // setup the flight index to be filterable
        // $flightIndex = $this->amadeusResult->response->flightIndex;
        // $this->mapFlightIndex($flightIndex)

        $offers = $this->amadeusResult->response->recommendation;
        if (!is_array($offers)) {
            $offers = [$offers];
        }

        foreach ($offers as $offer) {
            $result = new SearchResponse\Result();
            $offset = $searchResponse->getResult()->count();

            $result
                ->setItinerary(new SearchResponse\ItineraryResult())
                ->getItinerary()
                    ->setLegs($this->mapLegs($offset));

            $searchResponse->getResult()->add($result);
        }

        $this->mappedResponse = $searchResponse;
    }

    /**
     * Method to setup the leg collection for an itinerary
     * @param int $offset
     * @return ArrayCollection
     */
    private function mapLegs($offset)
    {
        $legCollection = new ArrayCollection();
        $flightIndex = $this->amadeusResult->response->flightIndex;

        if (!is_array($flightIndex)) {
            $flightIndex = [$flightIndex];
        }

        foreach ($flightIndex as $itineraryDirection) {
            $directionLegCollection = new ArrayCollection();
            $groupOfFlights = $itineraryDirection->groupOfFlights;

            if (!is_array($groupOfFlights)) {
                $groupOfFlights = [$groupOfFlights];
            }
            $groupOfFlights = new \ArrayObject($groupOfFlights);

            $group = $groupOfFlights->offsetGet($offset);
            if (!is_array($group)) {
                $group = [$group];
            }

            foreach ($group as $leg) {
                $itineraryLeg = new SearchResponse\Leg();
                $itineraryLeg->setSegments(new ArrayCollection());

                $segments = $leg->flightDetails;
                if (!is_array($segments)) {
                    $segments = [$segments];
                }

                foreach ($segments as $segment) {
                    $legSegment = new SearchResponse\Segment();

                    // set arrival and departure
                    $legSegment
                        ->setAirports(new SearchResponse\Airports())
                        ->getAirports()
                        ->setDeparture(new SearchResponse\Location())
                        ->getDeparture()
                        ->setIata($segment->flightInformation->location[0]->locationId);

                    $legSegment
                        ->getAirports()
                        ->setArrival(new SearchResponse\Location())
                        ->getArrival()
                        ->setIata($segment->flightInformation->location[1]->locationId);

                    $itineraryLeg->getSegments()->add($legSegment);
                }

                $directionLegCollection->add($itineraryLeg);
            }

            $legCollection->add($directionLegCollection);
        }

        return $legCollection;
    }

    /**
     * @return SearchResponse
     */
    public function getMappedResponse()
    {
        return $this->mappedResponse;
    }

    /**
     * @return string
     */
    public function getMappedResponseAsJson()
    {
        return $this->mapper->createJson($this->getMappedResponse());
    }
}