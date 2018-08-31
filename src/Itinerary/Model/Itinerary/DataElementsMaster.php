<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster\ElementManagementData;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster\Ssr;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster\Ticket;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\FreetextData\FreetextDetail;
use Flight\Service\Amadeus\Itinerary\Model\Remark;

/**
 * DataElementsMAster Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class DataElementsMaster extends AbstractModel
{
    /**
     * @var ArrayCollection
     */
    private $elementManagementData;

    /**
     * @var FreetextDetail
     */
    private $otherDataFreetext;

    /**
     * @var
     */
    private $ticketElement;

    /**
     * @var ArrayCollection
     */
    private $serviceRequest;

    /**
     * @var ArrayCollection
     */
    private $remarks;

    /**
     * DataElementsMaster constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->elementManagementData = new ArrayCollection();
        $this->otherDataFreetext     = new FreetextDetail();
        $this->ticketElement         = new Ticket();
        $this->serviceRequest        = new ArrayCollection();
        $this->remarks               = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return ArrayCollection
     */
    public function getElementManagementData() : ?ArrayCollection
    {
        return $this->elementManagementData;
    }

    /**
     * @param ArrayCollection $elementManagementData
     *
     * @return DataElementsMaster
     */
    public function setElementManagementData(ArrayCollection $elementManagementData) : DataElementsMaster
    {
        $this->elementManagementData = $elementManagementData;
        return $this;
    }

    /**
     * @return FreetextDetail
     */
    public function getOtherDataFreetext() : ?FreetextDetail
    {
        return $this->otherDataFreetext;
    }

    /**
     * @param FreetextDetail $otherDataFreetext
     *
     * @return DataElementsMaster
     */
    public function setOtherDataFreetext(FreetextDetail $otherDataFreetext) : DataElementsMaster
    {
        $this->otherDataFreetext = $otherDataFreetext;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTicketElement()
    {
        return $this->ticketElement;
    }

    /**
     * @param mixed $ticketElement
     *
     * @return DataElementsMaster
     */
    public function setTicketElement($ticketElement)
    {
        $this->ticketElement = $ticketElement;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getServiceRequest() : ?ArrayCollection
    {
        return $this->serviceRequest;
    }

    /**
     * @param ArrayCollection $serviceRequest
     *
     * @return DataElementsMaster
     */
    public function setServiceRequest(ArrayCollection $serviceRequest) : DataElementsMaster
    {
        $this->serviceRequest = $serviceRequest;
        return $this;
    }

    /**
     * getter for remarks
     *
     * @return ArrayCollection
     */
    public function getRemarks(): ArrayCollection
    {
        return $this->remarks;
    }

    /**
     * setter for remarks
     * @param ArrayCollection $remarks
     *
     * @return $this
     */
    public function setRemarks(ArrayCollection $remarks): DataElementsMaster
    {
        $this->remarks = $remarks;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->mapDataElementsIndiv($data->{'dataElementsIndiv'});
    }

    /**
     * @param $data
     */
    private function mapDataElementsIndiv($data)
    {
        if (is_array($data)) {
            /** @var \stdClass $elementManagementData */
            foreach ($data as $elementManagementData) {
                if (isset($elementManagementData->serviceRequest)) {
                    $dataElement = new ElementManagementData($elementManagementData->{'elementManagementData'});
                    $this->elementManagementData->add($dataElement);
                }
                if (isset($elementManagementData->otherDataFreetext)) {
                    $this->otherDataFreetext->populate($elementManagementData->{'otherDataFreetext'});
                }
                if (isset($elementManagementData->ticketElement)
                    && isset($elementManagementData->{'ticketElement'}->ticket)
                ) {
                    $this->ticketElement->populate($elementManagementData->{'ticketElement'}->{'ticket'});
                }
                if (isset($elementManagementData->serviceRequest)
                    && isset($elementManagementData->{'serviceRequest'}->ssr)) {
                    $this->serviceRequest->add(new Ssr($elementManagementData->{'serviceRequest'}->{'ssr'}));
                }
                if (isset($elementManagementData->miscellaneousRemarks)
                    && isset($elementManagementData->{'miscellaneousRemarks'}->remarks)) {
                    $this->remarks->add(
                        new Remark($elementManagementData->{'miscellaneousRemarks'}->{'remarks'})
                    );
                }
            }
        } else {
            if (isset($data->elementManagementData)) {
                $dataElement = new ElementManagementData($data->{'elementManagementData'});
                $this->elementManagementData->add($dataElement);
            }
            if (isset($data->otherDataFreetext)) {
                $this->otherDataFreetext->populate($data->{'otherDataFreetext'});
            }
            if (isset($data->ticketElement) && isset($elementManagementData->{'ticketElement'}->{'ticket'})) {
                $this->ticketElement->populate($data->{'ticket'});
            }
            if (isset($data->serviceRequest)) {
                $this->serviceRequest->add(new Ssr($data->{'serviceRequest'}->{'ssr'}));
            }
            if (isset($data->miscellaneousRemarks)
                && isset($data->{'miscellaneousRemarks'}->remarks)) {
                $this->remarks->add(
                    new Remark($data->{'miscellaneousRemarks'}->{'remarks'})
                );
            }
        }
    }
}
