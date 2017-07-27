<?php
namespace AmadeusService\Search\Exception;

use AmadeusService\Application\Exception\ServiceException;
use Throwable;

/**
 * Class SearchRequestFailedException
 * @package AmadeusService\Search\Exception
 */
class ServiceRequestAuthenticationFailedException extends ServiceException
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
        return 'The `Amadeus\Client::securityAuthenticate` method didn\'t return state OK';
    }
}