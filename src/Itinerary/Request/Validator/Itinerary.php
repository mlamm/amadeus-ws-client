<?php

namespace Flight\Service\Amadeus\Itinerary\Request\Validator;

use Flight\Service\Amadeus\Itinerary\Exception\InvalidRequestParameterException;
use Particle\Validator\Validator;

/**
 * Class Description
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class Itinerary
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * Itinerary constructor.
     *
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }
    
    /**
     * validate session
     *
     * @param $session
     *
     * @throws InvalidRequestParameterException
     */
    public function validateSession($session)
    {
        $validator = new Validator();
        $validator->required('session-id')->string();
        $validator->required('security-token')->string();
        $validator->required('sequence-number')->integer();

        $validationResult = $validator->validate((array) $session);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }

    /**
     * validate authentication
     *
     * @param $authentication
     *
     * @throws InvalidRequestParameterException
     */
    public function validateAuthentication($authentication)
    {
        $validator = new Validator();
        $validator->required('office-id')->string();
        $validator->required('user-id')->string();
        $validator->required('password-data')->string();
        $validator->required('password-length')->integer();
        $validator->required('duty-code')->string();
        $validator->required('organization')->string();

        $validationResult = $validator->validate((array) $authentication);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }

    /**
     * validate recordLocator
     *
     * @param string $recordLocator
     *
     * @throws InvalidRequestParameterException
     */
    public function validateRecordLocator(string $recordLocator)
    {
        $validator = new Validator();
        $validator->required('recordLocator')->alnum();
        $validationResult = $validator->validate(['recordLocator' => $recordLocator]);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }
}