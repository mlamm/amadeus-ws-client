<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\OriginDestinationDetails\ItineraryInfo;

/**
 * OriginDestinationDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class OriginDestinationDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $originDestination;

    /**
     * @var ArrayCollection
     */
    private $itineraryInfo;

    /**
     * @return string
     */
    public function getOriginDestination() : ?string
    {
        return $this->originDestination;
    }

    /**
     * @param string $originDestination
     *
     * @return OriginDestinationDetails
     */
    public function setOriginDestination(string $originDestination) : OriginDestinationDetails
    {
        $this->originDestination = $originDestination;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItineraryInfo() : ?ArrayCollection
    {
        return $this->itineraryInfo;
    }

    /**
     * @param ArrayCollection $itineraryInfo
     *
     * @return OriginDestinationDetails
     */
    public function setItineraryInfo(ArrayCollection $itineraryInfo) : OriginDestinationDetails
    {
        $this->itineraryInfo = $itineraryInfo;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (!empty((array) $data->originDestination)) {
            $this->originDestination= $data->originDestination ?? null;
        }
        if (isset($data->itineraryInfo)) {
            $this->itineraryInfo = $this->mapItineraryInfo($data->{'itineraryInfo'});
        }
    }

    /**
     * @param $data
     *
     * @return ArrayCollection
     */
    private function mapItineraryInfo($data) : ?ArrayCollection
    {
        $collection = new ArrayCollection();

        if (is_array($data)) {
            foreach ($data as $info) {
                $itinInfo = new ItineraryInfo($info);
                $collection->add($itinInfo);
            }
        } else {
            $itinInfo = new ItineraryInfo($data);
            $collection->add($itinInfo);
        }
        return $collection;
    }
}
