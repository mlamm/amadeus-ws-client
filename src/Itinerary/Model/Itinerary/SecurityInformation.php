<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation\QueueingInformation;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation\ResponsibilityInformation;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SecurityInformation\SecondRpInformation;

/**
 * SecurityInformation Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SecurityInformation extends AbstractModel
{
    /**
     * @var ResponsibilityInformation
     */
    private $responsibilityInformation;

    /**
     * @var QueueingInformation
     */
    private $queueingInformation;

    /**
     * @var SecondRpInformation
     */
    private $secondRpInformation;

    /**
     * @var string
     */
    private $cityCode;

    /**
     * SecurityInformation constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->responsibilityInformation = new ResponsibilityInformation();
        $this->queueingInformation       = new QueueingInformation();
        $this->secondRpInformation       = new SecondRpInformation();

        parent::__construct($data);
    }

    /**
     * @return ResponsibilityInformation
     */
    public function getResponsibilityInformation() : ?ResponsibilityInformation
    {
        return $this->responsibilityInformation;
    }

    /**
     * @param ResponsibilityInformation $responsibilityInformation
     *
     * @return SecurityInformation
     */
    public function setResponsibilityInformation(ResponsibilityInformation $responsibilityInformation) : ?SecurityInformation
    {
        $this->responsibilityInformation = $responsibilityInformation;
        return $this;
    }

    /**
     * @return QueueingInformation
     */
    public function getQueueingInformation() : ?QueueingInformation
    {
        return $this->queueingInformation;
    }

    /**
     * @param QueueingInformation $queueingInformation
     *
     * @return SecurityInformation
     */
    public function setQueueingInformation(QueueingInformation $queueingInformation) : SecurityInformation
    {
        $this->queueingInformation = $queueingInformation;
        return $this;
    }

    /**
     * @return SecondRpInformation
     */
    public function getSecondRpInformation() : ?SecondRpInformation
    {
        return $this->secondRpInformation;
    }

    /**
     * @param SecondRpInformation $secondRpInformation
     *
     * @return SecurityInformation
     */
    public function setSecondRpInformation(SecondRpInformation $secondRpInformation) : SecurityInformation
    {
        $this->secondRpInformation = $secondRpInformation;
        return $this;
    }

    /**
     * @return string
     */
    public function getCityCode() : ?string
    {
        return $this->cityCode;
    }

    /**
     * @param string $cityCode
     *
     * @return SecurityInformation
     */
    public function setCityCode(string $cityCode) : SecurityInformation
    {
        $this->cityCode = $cityCode;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return SecurityInformation
     */
    public function populate(\stdClass $data) : SecurityInformation
    {
        $this->responsibilityInformation = new ResponsibilityInformation($data->{'responsibilityInformation'});
        $this->queueingInformation       = new QueueingInformation($data->{'queueingInformation'});
        $this->secondRpInformation       = new SecondRpInformation($data->{'secondRpInformation'});
        $this->cityCode                 = $data->cityCode;

        return $this;
    }
}
