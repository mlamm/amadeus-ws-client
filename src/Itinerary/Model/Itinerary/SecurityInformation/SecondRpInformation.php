<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Itinerary SecondRpInformation Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SecondRpInformation extends AbstractModel
{
    /**
     * @var string
     */
    private $creationOfficeId;

    /**
     * @var string
     */
    private $agentSignature;

    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var string
     */
    private $creatorIataCode;

    /**
     * @var string
     */
    private $creationTime;

    /**
     * @return string
     */
    public function getCreationOfficeId() : ?string
    {
        return $this->creationOfficeId;
    }

    /**
     * @param string $creationOfficeId
     *
     * @return SecondRpInformation
     */
    public function setCreationOfficeId(string $creationOfficeId) : SecondRpInformation
    {
        $this->creationOfficeId = $creationOfficeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgentSignature() : ?string
    {
        return $this->agentSignature;
    }

    /**
     * @param string $agentSignature
     *
     * @return SecondRpInformation
     */
    public function setAgentSignature(string $agentSignature) : SecondRpInformation
    {
        $this->agentSignature = $agentSignature;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationDate() : ?string
    {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     *
     * @return SecondRpInformation
     */
    public function setCreationDate(string $creationDate) : SecondRpInformation
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatorIataCode() : ?string
    {
        return $this->creatorIataCode;
    }

    /**
     * @param string $creatorIataCode
     *
     * @return SecondRpInformation
     */
    public function setCreatorIataCode(string $creatorIataCode) : SecondRpInformation
    {
        $this->creatorIataCode = $creatorIataCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationTime() : ?string
    {
        return $this->creationTime;
    }

    /**
     * @param string $creationTime
     *
     * @return SecondRpInformation
     */
    public function setCreationTime(string $creationTime) : SecondRpInformation
    {
        $this->creationTime = $creationTime;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return SecondRpInformation
     */
    public function populate(\stdClass $data) : SecondRpInformation
    {
        $this->creationOfficeId = $data->{'creationOfficeId'} ?? null;
        $this->agentSignature   = $data->{'agentSignature'} ?? null;
        $this->creationDate     = $data->{'creationDate'} ?? null;
        $this->creationTime     = $data->{'creationTime'} ?? null;
        $this->creatorIataCode  = $data->{'creatorIataCode'} ?? null;

        return $this;
    }
}