<?php
namespace Flight\Service\Amadeus\Session\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Class InvalidRequestException
 * @package Flight\Service\Amadeus\Remarks\Exception
 */
class InvalidRequestException extends ServiceException
{
    /**
     * @inheritdoc
     */
    public function getInternalErrorCode()
    {
        return 'ARS0001';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage()
    {
        return 'MALFORMED REQUEST';
    }
}
