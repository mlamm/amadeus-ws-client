<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\FreetextData\FreetextDetail;

/**
 * FreetextData Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class FreetextData extends AbstractModel
{
    /**
     * @var FreetextDetail
     */
    private $freetextDetail;

    /**
     * @var string
     */
    private $longFreetext;

    /**
     * FreetextData constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->freetextDetail = new FreetextDetail();

        parent::__construct($data);
    }

    /**
     * @return FreetextDetail
     */
    public function getFreetextDetail() : ?FreetextDetail
    {
        return $this->freetextDetail;
    }

    /**
     * @param FreetextDetail $freetextDetail
     *
     * @return FreetextData
     */
    public function setFreetextDetail(FreetextDetail $freetextDetail) : FreetextData
    {
        $this->freetextDetail = $freetextDetail;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongFreetext() : ?string
    {
        return $this->longFreetext;
    }

    /**
     * @param string $longFreetext
     *
     * @return FreetextData
     */
    public function setLongFreetext(string $longFreetext) : FreetextData
    {
        $this->longFreetext = $longFreetext;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return $this
     */
    public function populate(\stdClass $data) : FreetextData
    {
        $this->longFreetext   = $data->{'longFreetext'};
        $this->freetextDetail->populate($data->{'freetextDetail'});

        return $this;
    }
}
