<?php

namespace Flight\Service\Amadeus\Itinerary\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Response\ResultResponse;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary;

/**
 * Class AmadeusResponseTransformer
 *
 * @package Flight\Service\Amadeus\Remarks\Model
 */
class AmadeusResponseTransformer
{
    /**
     * maps the result to an itinerary which can be sent back to the client
     *
     * @param Result $result
     *
     * @return ResultResponse
     */
    public function mapResult(Result $result)
    {
        $responseResult = new ResultResponse();
        $itinerary      = new Itinerary();

        if (isset($result->response->pnrHeader)) {
            $itinerary->setPnrHeader(new Itinerary\PnrHeader($result->response->pnrHeader));
        }
        if (isset($result->response->{'securityInformation'})) {
            $itinerary->setSecurityInformation(
                new Itinerary\SecurityInformation($result->response->{'securityInformation'})
            );
        }
        if (isset($result->response->{'freetextData'})) {
            $itinerary->setFreetextData(new Itinerary\FreetextData($result->response->{'freetextData'}));
        }
        if (isset($result->response->{'sbrPOSDetails'})) {
            $itinerary->setSbrPOSDetails(new Itinerary\SbrPosDetails($result->response->{'sbrPOSDetails'}));
        }
        if (isset($result->response->{'sbrCreationPosDetails'})) {
            $itinerary->setSbrCreationPosDetails(
                new Itinerary\SbrCreationPosDetails($result->response->{'sbrCreationPosDetails'})
            );
        }
        if (isset($result->response->{'sbrUpdatorPosDetails'})) {
            $itinerary->setSbrUpdatorPosDetails(
                new Itinerary\SbrUpdatorPosDetails($result->response->{'sbrUpdatorPosDetails'})
            );
        }
        if (isset($result->response->{'technicalData'})) {
            $itinerary->setTechnicalData(new Itinerary\TechnicalData($result->response->{'technicalData'}));
        }
        if (isset($result->response->{'travellerInfo'})) {
            $itinerary->setTravellerInfo($this->mapTravellerInfo($result->response->{'travellerInfo'}));
        }
        if (isset($result->response->{'originDestinationDetails'})) {
            $itinerary->setOriginDestinationDetails(
                new Itinerary\OriginDestinationDetails($result->response->{'originDestinationDetails'})
            );
        }
        if (isset($result->response->{'segmentGroupingInfo'})) {
            $itinerary->setSegmentGroupingInfo($this->mapSegmentGroupInfo($result->response->{'segmentGroupingInfo'}));
        }
        if (isset($result->response->{'dataElementsMaster'})) {
            $itinerary->setDataElementsMaster($this->mapDataElementsMaster($result->response->{'dataElementsMaster'}));
        }
        $responseResult->setResult($itinerary);
        return $responseResult;
    }

    /**
     * @param $data
     *
     * @return ArrayCollection
     */
    public function mapTravellerInfo($data)
    {
        $collection = new ArrayCollection();

        if (is_array($data)) {
            /** @var \stdClass $travellerInfo */
            foreach ($data as $travellerInfo) {
                $traveller = new Itinerary\TravellerInfo($travellerInfo);
                $collection->add($traveller);
            }
        } else {
            $traveller = new Itinerary\TravellerInfo($data);
            $collection->add($traveller);
        }
        return $collection;
    }

    /**
     * map the segmentGroupInfo into ArrayCollection
     * @param $data
     *
     * @return ArrayCollection
     */
    public function mapSegmentGroupInfo($data)
    {
        $collection = new ArrayCollection();

        if (is_array($data)) {
            /** @var \stdClass $segmentInfo */
            foreach ($data as $segmentInfo) {
                $segment = new Itinerary\SegmentGroupingInfo($segmentInfo);
                $collection->add($segment);
            }
        } else {
            $segment = new Itinerary\SegmentGroupingInfo($data);
            $collection->add($segment);
        }
        return $collection;
    }

    /**
     * map the DataElementsMaster entry/entries
     * @param $data
     *
     * @return ArrayCollection
     */
    public function mapDataElementsMaster($data)
    {
        $collection = new ArrayCollection();

        if (is_array($data)) {
            /** @var \stdClass $elementManagementData */
            foreach ($data as $elementManagementData) {
                $dataElement = new Itinerary\DataElementsMaster($elementManagementData);
                $collection->add($dataElement);
            }
        } else {
            $dataElement = new Itinerary\DataElementsMaster($data);
            $collection->add($dataElement);
        }
        return $collection;
    }

    /**
     * @see $this->mapResultRemarksRead
     *
     * @param Result $result
     *
     * @return ResultResponse
     */
    public function mapResultRemarksAdd(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

    /**
     * @see $this->mapResultRemarksRead
     *
     * @param Result $result
     *
     * @return ResultResponse
     */
    public function mapResultRemarksDelete(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

}
