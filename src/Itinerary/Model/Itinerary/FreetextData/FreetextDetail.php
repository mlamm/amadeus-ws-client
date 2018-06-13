<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\FreetextData;

/**
 * FreetextDetail Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class FreetextDetail
{
    /**
     * @var string
     */
    private $subjectQualifier;

    /**
     * @var string
     */
    private $longFreetext;

    /**
     * @var string
     */
    private $type;

    /**
     * FreetextDetail constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        if (null != $data) {
            $this->populate($data);
        }
    }

    /**
     * @return string
     */
    public function getSubjectQualifier() : ?string
    {
        return $this->subjectQualifier;
    }

    /**
     * @param string $subjectQualifier
     *
     * @return FreetextDetail
     */
    public function setSubjectQualifier(string $subjectQualifier) : FreetextDetail
    {
        $this->subjectQualifier = $subjectQualifier;
        return $this;
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
     * @return FreetextDetail
     */
    public function setType(string $type) : FreetextDetail
    {
        $this->type = $type;
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
     * @return FreetextDetail
     */
    public function setLongFreetext(string $longFreetext) : FreetextDetail
    {
        $this->longFreetext = $longFreetext;
        return $this;
    }

    /**
     * populate data from stdClass
     *
     * @param \stdClass $data
     *
     * @return FreetextDetail
     */
    public function populate(\stdClass $data) : FreetextDetail
    {
        $this->subjectQualifier = $data->{'subjectQualifier'} ?? null;
        $this->type             = $data->{'type'} ?? null;
        $this->longFreetext     = $data->{'longFreetext'} ?? null;

        return $this;
    }
}
