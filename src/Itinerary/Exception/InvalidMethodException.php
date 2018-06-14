<?php

namespace Flight\Service\Amadeus\Itinerary\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Class InvalidMethodException
 *
 * @package Flight\Service\Amadeus\Itinerary\Exception
 */
class InvalidMethodException extends ServiceException
{
    const INTERNAL_ERROR_CODE = 'ARS0006';

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
        return 'INVALID INTERNAL METHOD CALL' . ' - ' . $this->getMessage();
    }
}
