<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrPreferences;

/**
 * UserPreferences Model
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class UserPreferences
{
    /**
     * @var string
     */
    private $codedCountry;

    /**
     * UserPreferences constructor.
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
    public function getCodedCountry() : ?string
    {
        return $this->codedCountry;
    }

    /**
     * @param string $codedCountry
     *
     * @return UserPreferences
     */
    public function setCodedCountry(string $codedCountry) : UserPreferences
    {
        $this->codedCountry = $codedCountry;
        return $this;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->codedCountry = $data->{'codedCountry'} ?? null;
    }
}
