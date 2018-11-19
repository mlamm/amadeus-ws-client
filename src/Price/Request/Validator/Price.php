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
     * Validate session header of request.
     *
     * @param $sessionHeader
     *
     * @throws InvalidRequestParameterException
     */
    public function validateSession($sessionHeader)
    {
        $validator = new Validator();
        $validator->required('session-id')->string();
        $validator->required('security-token')->string();
        $validator->required('sequence-number')->integer();

        $validationResult = $validator->validate((array) $sessionHeader);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }

    /**
     * Validate body parameters for price create.
     *
     * @param \stdClass|null $body body content
     * @throws InvalidRequestParameterException
     */
    public function validatePostBody($body)
    {
        $validator = new Validator();
        $validator->required('tariff')->string();
        $validator->required('tariff')->inArray([
            'IATA',
            'NEGO',
            'NETALLU000867',
            'NETALLU513058',
            'NETALLU176212',
            'NETALLU374186',
            'NETALLU020481',
            'CALCPUB',
        ]);
        $validator->optional('fare-family')->string();

        $validationResult = $validator->validate((array) $body);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }
}
