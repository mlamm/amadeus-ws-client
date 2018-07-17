<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\OriginDestinationDetails;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\ElementManagementItinerary;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\ItineraryMessageAction;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\ItineraryReservationInfo;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\RelatedProduct;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\SelectionDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

/**
 * ItineraryInfo Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryInfo extends AbstractModel
{
    /**
     * @var ElementManagementItinerary
     */
    private $elementManagementItinerary;

    /**
     * @var TravelProduct
     */
    private $travelProduct;

    /**
     * @var ItineraryMessageAction
     */
    private $itineraryMessageAction;

    /**
     * @var ItineraryReservationInfo
     */
    private $itineraryReservationInfo;

    /**
     * @var RelatedProduct
     */
    private $relatedProduct;

    /**
     * @var FlightDetail
     */
    private $flightDetail;

    /**
     * @var SelectionDetails
     */
    private $selectionDetails;

    /**
     * ItineraryInfo constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->elementManagementItinerary = new ElementManagementItinerary();
        $this->travelProduct              = new TravelProduct();
        $this->itineraryMessageAction     = new ItineraryMessageAction();
        $this->itineraryReservationInfo   = new ItineraryReservationInfo();
        $this->relatedProduct             = new RelatedProduct();
        $this->flightDetail               = new FlightDetail();
        $this->selectionDetails           = new SelectionDetails();

        parent::__construct($data);
    }

    /**
     * @return ElementManagementItinerary
     */
    public function getElementManagementItinerary() : ?ElementManagementItinerary
    {
        return $this->elementManagementItinerary;
    }

    /**
     * @param ElementManagementItinerary $elementManagementItinerary
     *
     * @return ItineraryInfo
     */
    public function setElementManagementItinerary(ElementManagementItinerary $elementManagementItinerary) : ItineraryInfo
    {
        $this->elementManagementItinerary = $elementManagementItinerary;
        return $this;
    }

    /**
     * @return TravelProduct
     */
    public function getTravelProduct() : ?TravelProduct
    {
        return $this->travelProduct;
    }

    /**
     * @param TravelProduct $travelProduct
     *
     * @return ItineraryInfo
     */
    public function setTravelProduct(TravelProduct $travelProduct) : ItineraryInfo
    {
        $this->travelProduct = $travelProduct;
        return $this;
    }

    /**
     * @return ItineraryMessageAction
     */
    public function getItineraryMessageAction() : ?ItineraryMessageAction
    {
        return $this->itineraryMessageAction;
    }

    /**
     * @param ItineraryMessageAction $itineraryMessageAction
     *
     * @return ItineraryInfo
     */
    public function setItineraryMessageAction(ItineraryMessageAction $itineraryMessageAction) : ItineraryInfo
    {
        $this->itineraryMessageAction = $itineraryMessageAction;
        return $this;
    }

    /**
     * @return ItineraryReservationInfo
     */
    public function getItineraryReservationInfo() : ?ItineraryReservationInfo
    {
        return $this->itineraryReservationInfo;
    }

    /**
     * @param ItineraryReservationInfo $itineraryReservationInfo
     *
     * @return ItineraryInfo
     */
    public function setItineraryReservationInfo(ItineraryReservationInfo $itineraryReservationInfo) : ItineraryInfo
    {
        $this->itineraryReservationInfo = $itineraryReservationInfo;
        return $this;
    }

    /**
     * @return RelatedProduct
     */
    public function getRelatedProduct() : ?RelatedProduct
    {
        return $this->relatedProduct;
    }

    /**
     * @param RelatedProduct $relatedProduct
     *
     * @return ItineraryInfo
     */
    public function setRelatedProduct(RelatedProduct $relatedProduct) : ItineraryInfo
    {
        $this->relatedProduct = $relatedProduct;
        return $this;
    }

    /**
     * @return FlightDetail
     */
    public function getFlightDetail() : ?FlightDetail
    {
        return $this->flightDetail;
    }

    /**
     * @param FlightDetail $flightDetail
     *
     * @return ItineraryInfo
     */
    public function setFlightDetail(FlightDetail $flightDetail) : ItineraryInfo
    {
        $this->flightDetail = $flightDetail;
        return $this;
    }

    /**
     * @return SelectionDetails
     */
    public function getSelectionDetails() : ?SelectionDetails
    {
        return $this->selectionDetails;
    }

    /**
     * @param SelectionDetails $selectionDetails
     *
     * @return ItineraryInfo
     */
    public function setSelectionDetails(SelectionDetails $selectionDetails) : ItineraryInfo
    {
        $this->selectionDetails = $selectionDetails;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->elementManagementItinerary)) {
            $this->elementManagementItinerary->populate($data->{'elementManagementItinerary'});
        }
        if (isset($data->travelProduct)) {
            $this->travelProduct->populate($data->{'travelProduct'});
        }
        if (isset($data->itineraryMessageAction)) {
            $this->itineraryMessageAction->populate($data->{'itineraryMessageAction'});
        }
        if (isset($data->itineraryReservationInfo)) {
            $this->itineraryReservationInfo->populate($data->{'itineraryReservationInfo'});
        }
        if (isset($data->relatedProduct)) {
            $this->relatedProduct->populate($data->{'relatedProduct'});
        }
        if (isset($data->flightDetail)) {
            $this->flightDetail->populate($data->{'flightDetail'});
        }
        if (isset($data->selectionDetails)) {
            $this->selectionDetails->populate($data->{'selectionDetails'});
        }
    }
}
