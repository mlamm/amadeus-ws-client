<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail\ArrivalStationInfo;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail\Facilities;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\FlightDetail\ProductDetails;

/**
 * FlightDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class FlightDetail extends AbstractModel
{
    /**
     * @var ProductDetails
     */
    private $productDetails;

    /**
     * @var ArrivalStationInfo
     */
    private $arrivalStationInfo;


    private $facilities;

    /**
     * FlightDetail constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->productDetails     = new ProductDetails();
        $this->arrivalStationInfo = new ArrivalStationInfo();
        $this->facilities         = new Facilities();

        parent::__construct($data);
    }

    /**
     * @return ProductDetails
     */
    public function getProductDetails() : ?ProductDetails
    {
        return $this->productDetails;
    }

    /**
     * @param ProductDetails $productDetails
     *
     * @return FlightDetail
     */
    public function setProductDetails(ProductDetails $productDetails) : FlightDetail
    {
        $this->productDetails = $productDetails;
        return $this;
    }

    /**
     * @return ArrivalStationInfo
     */
    public function getArrivalStationInfo() : ?ArrivalStationInfo
    {
        return $this->arrivalStationInfo;
    }

    /**
     * @param ArrivalStationInfo $arrivalStationInfo
     *
     * @return FlightDetail
     */
    public function setArrivalStationInfo(ArrivalStationInfo $arrivalStationInfo) : FlightDetail
    {
        $this->arrivalStationInfo = $arrivalStationInfo;
        return $this;
    }

    /**
     * @return Facilities
     */
    public function getFacilities() : ?Facilities
    {
        return $this->facilities;
    }

    /**
     * @param Facilities $facilities
     *
     * @return FlightDetail
     */
    public function setFacilities(Facilities $facilities) : FlightDetail
    {
        $this->facilities = $facilities;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->productDetails)) {
            $this->productDetails->populate($data->{'productDetails'});
        }
        if (isset($data->arrivalStationInfo)) {
            $this->arrivalStationInfo->populate($data->{'arrivalStationInfo'});
        }
        if (isset($data->facilities)) {
            $this->facilities->populate($data->{'facilities'});
        }
    }
}
