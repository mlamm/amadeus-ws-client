<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\BoardpointDetail;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\CompanyDetail;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\OffpointDetail;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\Product;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\ProductDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct\TypeDetail;

/**
 * TravelProduct Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class TravelProduct extends AbstractModel
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var BoardpointDetail
     */
    private $boardpointDetail;

    /**
     * @var OffpointDetail
     */
    private $offpointDetail;

    /**
     * @var CompanyDetail
     */
    private $companyDetail;

    /**
     * @var ProductDetails
     */
    private $productDetails;

    /**
     * @var TypeDetail
     */
    private $typeDetail;

    /**
     * TravelProduct constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->product          = new Product();
        $this->boardpointDetail = new BoardpointDetail();
        $this->offpointDetail   = new OffpointDetail();
        $this->companyDetail    = new CompanyDetail();
        $this->productDetails   = new ProductDetails();
        $this->typeDetail       = new TypeDetail();

        parent::__construct($data);
    }

    /**
     * @return Product
     */
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return TravelProduct
     */
    public function setProduct(Product $product) : TravelProduct
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return BoardpointDetail
     */
    public function getBoardpointDetail() : ?BoardpointDetail
    {
        return $this->boardpointDetail;
    }

    /**
     * @param BoardpointDetail $boardpointDetail
     *
     * @return TravelProduct
     */
    public function setBoardpointDetail(BoardpointDetail $boardpointDetail) : TravelProduct
    {
        $this->boardpointDetail = $boardpointDetail;
        return $this;
    }

    /**
     * @return OffpointDetail
     */
    public function getOffpointDetail() : ?OffpointDetail
    {
        return $this->offpointDetail;
    }

    /**
     * @param OffpointDetail $offpointDetail
     *
     * @return TravelProduct
     */
    public function setOffpointDetail(OffpointDetail $offpointDetail) : TravelProduct
    {
        $this->offpointDetail = $offpointDetail;
        return $this;
    }

    /**
     * @return CompanyDetail
     */
    public function getCompanyDetail() : ?CompanyDetail
    {
        return $this->companyDetail;
    }

    /**
     * @param CompanyDetail $companyDetail
     *
     * @return TravelProduct
     */
    public function setCompanyDetail(CompanyDetail $companyDetail) : TravelProduct
    {
        $this->companyDetail = $companyDetail;
        return $this;
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
     * @return TravelProduct
     */
    public function setProductDetails(ProductDetails $productDetails) : TravelProduct
    {
        $this->productDetails = $productDetails;
        return $this;
    }

    /**
     * @return TypeDetail
     */
    public function getTypeDetail() : ?TypeDetail
    {
        return $this->typeDetail;
    }

    /**
     * @param TypeDetail $typeDetail
     *
     * @return TravelProduct
     */
    public function setTypeDetail(TypeDetail $typeDetail) : TravelProduct
    {
        $this->typeDetail = $typeDetail;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        if (isset($data->product)) {
            $this->product->populate($data->{'product'});
        }
        if (isset($data->boardpointDetail)) {
            $this->boardpointDetail->populate($data->{'boardpointDetail'});
        }
        if (isset($data->offpointDetail)) {
            $this->offpointDetail->populate($data->{'offpointDetail'});
        }
        if (isset($data->companyDetail)) {
            $this->companyDetail->populate($data->{'companyDetail'});
        }
        if (isset($data->productDetails)) {
            $this->productDetails->populate($data->{'productDetails'});
        }
        if (isset($data->typeDetail)) {
            $this->typeDetail->populate($data->{'typeDetail'});
        }
    }
}
