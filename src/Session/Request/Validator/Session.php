<?php

namespace Flight\Service\Amadeus\Session\Request\Validator;

use Flight\Service\Amadeus\Session\Exception\InvalidRequestParameterException;
use Particle\Validator\Validator;

/**
 * Session request validator
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Session
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * Session constructor.
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config  = $config;
    }

    /**
     * validate authentication
     *
     * @param $authentication
     *
     * @throws InvalidRequestParameterException
     */
    public function validateAuthentication($authentication): void
    {
        $validator = new Validator();
        $validator->required('office-id')->string();
        $validator->required('user-id')->string();
        $validator->required('password-data')->string();
        $validator->required('password-length')->integer();
        $validator->required('duty-code')->string();
        $validator->required('organization')->string();

        $validationResult = $validator->validate((array)$authentication);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
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
}
