<?php

namespace Flight\Service\Amadeus\Search\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Exception for missing system requirements.
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SystemRequirementException extends ServiceException
{
    const INTERNAL_ERROR_CODE = 'ARS0005';

    /**
     * Method to return the internal error code
     *
     * @return string
     */
    public function getInternalErrorCode()
    {
        return self::INTERNAL_ERROR_CODE;
    }

    /**
     * Method to return the internal error message.
     *
     * @return mixed
     */
    public function getInternalErrorMessage()
    {
        return $this->message;
    }
}