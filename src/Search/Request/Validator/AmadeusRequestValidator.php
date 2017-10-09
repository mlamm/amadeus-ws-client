<?php
declare(strict_types=1);

namespace AmadeusService\Search\Request\Validator;

use Particle\Validator\Exception\InvalidValueException;
use Particle\Validator\ValidationResult;
use Particle\Validator\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use AmadeusService\Search\Exception\InvalidRequestException;
use AmadeusService\Search\Exception\InvalidRequestParameterException;

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

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, \stdClass $config)
    {
        $this->logger  = $logger;
        $this->config  = $config;
    }

    /**
     * receives the request and prepares it for validation
     *
     * @param Request $request
     *
     * @throws InvalidRequestException
     * @throws InvalidRequestParameterException
     */
    public function validateRequest(Request $request) : void
    {
        $requestData = json_decode($request->getContent(), true);

        ///malformed request
        if (!is_array($requestData)) {
            throw new InvalidRequestException();
        }

        $validationResult = $this->doValidation($requestData);

        if ($validationResult->isNotValid()) {
            $ex = new InvalidRequestParameterException($validationResult->getFailures());

            throw $ex;
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
                if (count($element) > 20) {
                    throw new InvalidValueException(
                        'To many elements in filter-airline. Maximum is 20.',
                        'filter-airline'
                    );
                }
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
                $validator->optional('result-limit')->allowEmpty(false)->integer(true)->greaterThan(0);
                $validator->required('options');
                $validator->required('options.is-one-way-combination')->bool();
                $validator->required('options.is-overnight')->bool();
                $validator->required('options.is-area-search')->bool();
                $validator->required('options.is-benchmark')->bool();
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
