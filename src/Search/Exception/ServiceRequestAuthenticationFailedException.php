<?php
namespace AmadeusService\Search\Exception;

/**
 * Class SearchRequestFailedException
 * @package AmadeusService\Search\Exception
 */
class ServiceRequestAuthenticationFailedException extends AmadeusRequestException
{
    public function __construct(array $messages)
    {
        $this->internalErrorMessage = 'The `Amadeus\Client::securityAuthenticate` method didn\'t return state OK';
        parent::__construct($messages);
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorCode()
    {
        return 'ARS0001';
    }
}
