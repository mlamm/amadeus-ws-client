<?php
namespace AmadeusService\Search\Exception;

use AmadeusService\Application\Exception\ServiceException;
use Throwable;

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
        return 'ARS0003';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage()
    {
        return 'The provided request could not be mapped into the appropriate format';
    }
}