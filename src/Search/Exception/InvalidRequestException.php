<?php
namespace AmadeusService\Search\Exception;

use AmadeusService\Application\Exception\ServiceException;

/**
 * Class InvalidRequestException
 * @package AmadeusService\Search\Exception
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
