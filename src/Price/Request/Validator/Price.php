<?php

namespace Flight\Service\Amadeus\Price\Request\Validator;

use Flight\Service\Amadeus\Price\Exception\InvalidRequestParameterException;
use Particle\Validator\Validator;

/**
 * Price request validator
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Price
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * Price constructor.
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
     * validate Price
     *
     * @param $Price
     *
     * @throws InvalidRequestParameterException
     */
    public function validateSession($Price)
    {
        $validator = new Validator();
        $validator->required('session-id')->string();
        $validator->required('security-token')->string();
        $validator->required('sequence-number')->integer();

        $validationResult = $validator->validate((array) $Price);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }

    public function validatePostBody($body)
    {
        $validator = new Validator();
        $validator->required('tariff')->string();
        $validator->required('tariff')->inArray([
// %TODO
        ]);

        $validationResult = $validator->validate((array) $body);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }
}
