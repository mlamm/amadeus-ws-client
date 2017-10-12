<?php
namespace AmadeusService\Search\Exception;

use AmadeusService\Application\Exception\ServiceException;

/**
 * Class SearchRequestFailedException
 * @package AmadeusService\Search\Exception
 */
class MissingRequestParameterException extends ServiceException
{
    /**
     * @inheritdoc
     */
    public function getInternalErrorCode()
    {
        return 'ARS0002';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage()
    {
        return 'The provided search parameters do not suffice the necessary data to start a new search';
    }
}
