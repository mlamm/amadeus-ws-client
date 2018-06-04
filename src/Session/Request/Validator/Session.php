<?php

namespace Flight\Service\Amadeus\Session\Request\Validator;

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
     *@var \stdClass
     */
    protected $config;

    public function __construct(\stdClass $config)
    {
        $this->config  = $config;
    }

    /**
     * validate authentication
     *
     * @param $authentication
     *
     * @throws \Exception
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

        $validationResult = $validator->validate((array)$authentication);

        if ($validationResult->isNotValid()) {
            throw new \Exception($validationResult->getFailures());
        }
    }
}