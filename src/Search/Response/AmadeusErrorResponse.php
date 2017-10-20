<?php
declare(strict_types=1);

namespace AmadeusService\Search\Response;

use AmadeusService\Application\Response\ErrorResponse;
use AmadeusService\Search\Exception\ValidationException;
use Particle\Validator\Failure;
use Symfony\Component\HttpFoundation\Response;

/**
 * AmadeusErrorResponse.php
 *
 * Amadeus service error response
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
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
    public function addViolationFromValidationFailures(array $failures)
    {
        foreach ($failures as $failure) {
            $validationException = new ValidationException($failure->format());
            $validationException->setResponseCode(Response::HTTP_BAD_REQUEST);
            $this->addViolation($failure->getKey(), $validationException);
        }

        return $this;
    }
}
