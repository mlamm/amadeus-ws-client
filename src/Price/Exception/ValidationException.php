<?php
namespace Flight\Service\Amadeus\Price\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Class InvalidRequestParamException
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ValidationException extends ServiceException
{
    const INTERNAL_ERROR_CODE = 'ARS0002';

    /**
     * @inheritdoc
     */
    public function getInternalErrorCode() : string
    {
        return self::INTERNAL_ERROR_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage() : string
    {
        return 'INVALID OR MISSING REQUEST PARAM' . ' - ' . $this->getMessage();
    }
}
