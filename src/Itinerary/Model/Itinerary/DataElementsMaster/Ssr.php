<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\DataElementsMaster;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 * Ssr Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Ssr extends AbstractModel
{
    /**
     * @return string
     */
    private $type;

    /**
     * @return string
     */
    private $status;

    /**
     * @return string
     */
    private $companyId;

    /**
     * @return ArrayCollection
     */
    private $freeText;

    /**
     * Ssr constructor.
     *
     * @param null|\stdClass $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->freeText = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getType() : ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Ssr
     */
    public function setType($type) : Ssr
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Ssr
     */
    public function setStatus(string $status) : Ssr
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyId() : ?string
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     *
     * @return Ssr
     */
    public function setCompanyId($companyId) : Ssr
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFreeText() : ?ArrayCollection
    {
        return $this->freeText;
    }

    /**
     * @param ArrayCollection $freeText
     *
     * @return Ssr
     */
    public function setFreeText(ArrayCollection $freeText) : Ssr
    {
        $this->freeText = $freeText;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->type      = $data->{'type'} ?? null;
        $this->status    = $data->{'status'} ?? null;
        $this->companyId = $data->{'companyId'} ?? null;

        if (isset($data->freeText)) {
            if (is_array($data->freeText)) {
                $this->freeText = new ArrayCollection($data->{'freeText'});
            } else {
                $this->freeText->add($data->{'freeText'});
            }
        }
    }
}
