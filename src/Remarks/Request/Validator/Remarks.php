<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Request\Validator;

use Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException;
use Particle\Validator\Validator;

/**
 * AmadeusRequestValidator.php
 *
 * handles validation for client requests before requesting the Amadeus API
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Remarks
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

        $validationResult = $validator->validate((array)$authentication);

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
    public function validateRecordlocator(string $recordLocator)
    {
        $validator = new Validator();
        $validator->required('recordLocator')->alnum();
        $validationResult = $validator->validate(['recordLocator' => $recordLocator]);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }
}
