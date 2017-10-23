<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Request\Validator;

use Flight\Service\Amadeus\Search\Exception\InvalidRequestException;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException;
use Particle\Validator\Exception\InvalidValueException;
use Particle\Validator\ValidationResult;
use Particle\Validator\Validator;

/**
 * AmadeusRequestValidator.php
 *
 * handles validation for client requests before requesting the Y API
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestValidator
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
     * receives the request and prepares it for validation
     *
     * @param string $requestJson
     *
     * @throws InvalidRequestException
     * @throws InvalidRequestParameterException
     */
    public function validateRequest(string $requestJson) : void
    {
        $requestData = json_decode($requestJson, true);

        ///malformed request
        if (!is_array($requestData)) {
            throw new InvalidRequestException();
        }

        $validationResult = $this->doValidation($requestData);

        if ($validationResult->isNotValid()) {
            throw new InvalidRequestParameterException($validationResult->getFailures());
        }
    }

    /**
     * performs required validation of fields
     *
     * @param array $requestData
     *
     * @return ValidationResult
     */
    private function doValidation(array $requestData) : ValidationResult
    {
        $validator = new Validator();
        $allowedAgents       = $this->config->allowed_agents;
        $allowedTypes        = $this->config->allowed_types;
        $allowedCabinClasses = $this->config->allowed_cabin_class;

        $validator->required('agent')->string()->inArray($allowedAgents);
        $validator->required('adults')->integer(true);
        $validator->required('children')->integer(true);
        $validator->required('infants')->integer(true);
        $validator->optional('filter-cabin-class')->isArray()->callback(
            function ($element) use ($allowedCabinClasses) {
                foreach ($element as $cabinClass) {
                    if (!is_string($cabinClass) || !in_array(strtoupper($cabinClass), $allowedCabinClasses)) {
                        throw new InvalidValueException(
                            'Invalid cabin class value: ' . $cabinClass,
                            'filter-cabin-class'
                        );
                    }
                }

                return true;
            }
        );

        $validator->optional('filter-airline')->isArray()->callback(
            function ($element) {
                foreach ($element as $airlineCode) {
                    if (!is_string($airlineCode) || strlen($airlineCode) != 2) {
                        throw new InvalidValueException(
                            'Invalid airline code in filter-airline: ' . $airlineCode,
                            'filter-airline'
                        );
                    }
                }

                return true;
            }
        );

        $validator->required('legs')->each(
            function (Validator $validator) {
                $validator->required('departure');
                $validator->required('arrival');
                $validator->required('depart-at')->integer(true);
                $validator->required('is-flexible-date')->bool();
            }
        );

        $validator->required('business-cases');
        $validator->required('business-cases.0')->each(
            function (Validator $validator) use ($allowedTypes) {
                $validator->required('content-provider')->string()->equals('amadeus');
                $validator->required('type')->string()->inArray($allowedTypes);
                $validator->required('fare-type')->string();
                $validator->required('options');
                $validator->required('options.is-one-way-combination')->bool();
                $validator->required('options.is-overnight')->bool();
                $validator->required('options.is-area-search')->bool();
                $validator->required('options.is-benchmark')->bool();
                $validator->optional('options.result-limit')->allowEmpty(false)->integer(true)->greaterThan(0);
                $validator->required('authentication');
                $validator->required('authentication.office-id')->string();
                $validator->required('authentication.user-id')->string();
                $validator->required('authentication.password-data')->string();
                $validator->required('authentication.password-length')->integer(true);
                $validator->required('authentication.duty-code')->string();
                $validator->required('authentication.organization-id')->string();
            }
        );

        return $validator->validate($requestData);
    }
}
