<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\ItineraryInfo\TravelProduct;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * ProductDetails Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ProductDetails extends AbstractModel
{
    /**
     * @var string
     */
    private $identification;

    /**
     * @var string
     */
    private $classOfService;

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var string
     */
    private $description;

    /**
     * @return string
     */
    public function getIdentification() : ?string
    {
        return $this->identification;
    }

    /**
     * @param string $identification
     *
     * @return ProductDetails
     */
    public function setIdentification(string $identification) : ProductDetails
    {
        $this->identification = $identification;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassOfService() : ?string
    {
        return $this->classOfService;
    }

    /**
     * @param string $classOfService
     *
     * @return ProductDetails
     */
    public function setClassOfService(string $classOfService) : ProductDetails
    {
        $this->classOfService = $classOfService;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubtype() : ?string
    {
        return $this->subtype;
    }

    /**
     * @param string $subtype
     *
     * @return ProductDetails
     */
    public function setSubtype(string $subtype) : ProductDetails
    {
        $this->subtype = $subtype;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ProductDetails
     */
    public function setDescription(string $description) : ProductDetails
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->identification = $data->{'identification'} ?? null;
        $this->classOfService = $data->{'classOfService'} ?? null;
        $this->subtype = $data->{'subtype'} ?? null;
        $this->description = $data->{'description'} ?? null;
    }
}
