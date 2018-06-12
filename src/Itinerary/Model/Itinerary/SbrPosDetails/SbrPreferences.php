<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\SbrPosDetails\SbrPreferences\UserPreferences;

/**
 * Class Description
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class SbrPreferences
{
    /**
     * @var UserPreferences
     */
    private $userPreferences;

    /**
     * SbrPreferences constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->userPreferences = new UserPreferences();
        if (null != $data) {
            $this->populate($data);
        }
    }

    /**
     * @return UserPreferences
     */
    public function getUserPreferences() : ?UserPreferences
    {
        return $this->userPreferences;
    }

    /**
     * @param UserPreferences $userPreferences
     *
     * @return SbrPreferences
     */
    public function setUserPreferences(UserPreferences $userPreferences) : SbrPreferences
    {
        $this->userPreferences = $userPreferences;
        return $this;
    }

    /**
     * @param \stdClass $data
     *
     * @return SbrPreferences
     */
    public function populate(\stdClass $data) : SbrPreferences
    {
        if (isset($data->userPreferences)) {
            $this->userPreferences->populate($data->{'userPreferences'});
        }

        return $this;
    }
}
