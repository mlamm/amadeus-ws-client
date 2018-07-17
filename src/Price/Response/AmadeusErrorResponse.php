<?php

namespace Flight\Service\Amadeus\Price\Response;

use Flight\Service\Amadeus\Application\Response\ErrorResponse;
use Flight\Service\Amadeus\Price\Exception\ValidationException;
use Particle\Validator\Failure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Amadeus service error response
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusErrorResponse extends ErrorResponse
{
    /**
     * adds violation to violations stack
     *
     * @param Failure[] $failures
     *
     * @return ErrorResponse
     */
    public function addViolationFromValidationFailures(array $failures) : ErrorResponse
    {
        foreach ($failures as $failure) {
            $validationException = new ValidationException($failure->format());
            $validationException->setResponseCode(\Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
            $this->addViolation($failure->getKey(), $validationException);
        }

        return $this;
    }

    public static function notFound($data) : self
    {
        return new static($data, Response::HTTP_NOT_FOUND);
    }

    public static function serverError($data) : self
    {
        return new static($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
