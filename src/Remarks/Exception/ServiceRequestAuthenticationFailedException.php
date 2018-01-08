<?php
namespace Flight\Service\Amadeus\Remarks\Exception;

/**
 * Class RemarksRequestFailedException
 * @package Flight\Service\Amadeus\Remarks\Exception
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
        return 'ARS0003';
    }
}
