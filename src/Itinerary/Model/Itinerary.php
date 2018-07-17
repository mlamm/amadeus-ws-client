<?php
declare(strict_types = 1);

namespace Flight\Service\Amadeus\Itinerary\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Exception\InvalidMethodException;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\FreetextData;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\OriginDestinationDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\PnrHeader;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrCreationPosDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrUpdatorPosDetails;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\TechnicalData;

/**
 *
 * model for itinerary
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Itinerary extends AbstractModel
{
    /**
     * @var PnrHeader
     */
    private $pnrHeader;

    /**
     * @var SecurityInformation
     */
    private $securityInformation;

    /**
     * @var FreetextData
     */
    private $freetextData;

    /**
     * @var SbrPosDetails
     */
    private $sbrPOSDetails;

    /**
     * @var SbrCreationPosDetails
     */
    private $sbrCreationPosDetails;

    /**
     * @var SbrUpdatorPosDetails
     */
    private $sbrUpdatorPosDetails;

    /**
     * @var TechnicalData
     */
    private $technicalData;

    /**
     * @var ArrayCollection
     */
    private $travellerInfo;

    /**
     * @var ArrayCollection
     */
    private $dataElementsMaster;

    /**
     * @var OriginDestinationDetails
     */
    private $originDestinationDetails;

    /**
     * @var ArrayCollection
     */
    private $segmentGroupingInfo;

    /**
     * method not in use!
     *
     * @param \stdClass $data
     *
     * @throws InvalidMethodException
     */
    public function populate(\stdClass $data)
    {
        throw new InvalidMethodException(__METHOD__ . ' is not in use!');
    }

    /**
     * @return PnrHeader
     */
    public function getPnrHeader() : ?PnrHeader
    {
        return $this->pnrHeader;
    }

    /**
     * @param PnrHeader $pnrHeader
     *
     * @return Itinerary
     */
    public function setPnrHeader(PnrHeader $pnrHeader) : Itinerary
    {
        $this->pnrHeader = $pnrHeader;
        return $this;
    }

    /**
     * @return SecurityInformation
     */
    public function getSecurityInformation() : ?SecurityInformation
    {
        return $this->securityInformation;
    }

    /**
     * @param SecurityInformation $securityInformation
     *
     * @return Itinerary
     */
    public function setSecurityInformation(SecurityInformation $securityInformation) : Itinerary
    {
        $this->securityInformation = $securityInformation;
        return $this;
    }

    /**
     * @return FreetextData
     */
    public function getFreetextData() : ?FreetextData
    {
        return $this->freetextData;
    }

    /**
     * @param FreetextData $freetextData
     *
     * @return Itinerary
     */
    public function setFreetextData(FreetextData $freetextData) : Itinerary
    {
        $this->freetextData = $freetextData;
        return $this;
    }

    /**
     * @return SbrPosDetails
     */
    public function getSbrPOSDetails() : ?SbrPosDetails
    {
        return $this->sbrPOSDetails;
    }

    /**
     * @param SbrPosDetails $sbrPOSDetails
     *
     * @return Itinerary
     */
    public function setSbrPOSDetails(SbrPosDetails $sbrPOSDetails) : Itinerary
    {
        $this->sbrPOSDetails = $sbrPOSDetails;
        return $this;
    }

    /**
     * @return SbrCreationPosDetails
     */
    public function getSbrCreationPosDetails() : ?SbrCreationPosDetails
    {
        return $this->sbrCreationPosDetails;
    }

    /**
     * @param SbrCreationPosDetails $sbrCreationPosDetails
     *
     * @return Itinerary
     */
    public function setSbrCreationPosDetails(SbrCreationPosDetails $sbrCreationPosDetails) : Itinerary
    {
        $this->sbrCreationPosDetails = $sbrCreationPosDetails;
        return $this;
    }

    /**
     * @return SbrUpdatorPosDetails
     */
    public function getSbrUpdatorPosDetails() : ?SbrUpdatorPosDetails
    {
        return $this->sbrUpdatorPosDetails;
    }

    /**
     * @param SbrUpdatorPosDetails $sbrUpdatorPosDetails
     *
     * @return Itinerary
     */
    public function setSbrUpdatorPosDetails(SbrUpdatorPosDetails $sbrUpdatorPosDetails) : Itinerary
    {
        $this->sbrUpdatorPosDetails = $sbrUpdatorPosDetails;
        return $this;
    }

    /**
     * @return TechnicalData
     */
    public function getTechnicalData() : ?TechnicalData
    {
        return $this->technicalData;
    }

    /**
     * @param TechnicalData $technicalData
     *
     * @return Itinerary
     */
    public function setTechnicalData(TechnicalData $technicalData) : Itinerary
    {
        $this->technicalData = $technicalData;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTravellerInfo() : ?ArrayCollection
    {
        return $this->travellerInfo;
    }

    /**
     * @param ArrayCollection $travellerInfo
     *
     * @return Itinerary
     */
    public function setTravellerInfo(ArrayCollection $travellerInfo) : Itinerary
    {
        $this->travellerInfo = $travellerInfo;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDataElementsMaster() : ?ArrayCollection
    {
        return $this->dataElementsMaster;
    }

    /**
     * @param ArrayCollection $dataElementsMaster
     *
     * @return Itinerary
     */
    public function setDataElementsMaster(ArrayCollection $dataElementsMaster) : Itinerary
    {
        $this->dataElementsMaster = $dataElementsMaster;
        return $this;
    }

    /**
     * @return OriginDestinationDetails
     */
    public function getOriginDestinationDetails() : ?OriginDestinationDetails
    {
        return $this->originDestinationDetails;
    }

    /**
     * @param OriginDestinationDetails $originDestinationDetails
     *
     * @return Itinerary
     */
    public function setOriginDestinationDetails(OriginDestinationDetails $originDestinationDetails) : Itinerary
    {
        $this->originDestinationDetails = $originDestinationDetails;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSegmentGroupingInfo() : ?ArrayCollection
    {
        return $this->segmentGroupingInfo;
    }

    /**
     * @param ArrayCollection $segmentGroupingInfo
     *
     * @return Itinerary
     */
    public function setSegmentGroupingInfo(ArrayCollection $segmentGroupingInfo) : Itinerary
    {
        $this->segmentGroupingInfo = $segmentGroupingInfo;
        return $this;
    }
}
