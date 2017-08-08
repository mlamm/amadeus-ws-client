<?php
namespace AmadeusService\Search\Exception;

use AmadeusService\Application\Exception\ServiceException;
use Throwable;

/**
 * Class AmadeusRequestException
 * @package AmadeusService\Search\Exception
 */
class AmadeusRequestException extends ServiceException
{
    /**
     * @var \stdClass
     */
    protected $error;

    /**
     * @param \stdClass $error
     * @return $this
     */
    public function assignError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorCode()
    {
        return 'ARS000X';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage()
    {
        $errorCode = $this->error->applicationError->applicationErrorDetail->error;
        $errorText = $this->error->errorMessageText->description;
        if (!is_array($errorText)) {
            $errorText = [$errorText];
        }

        $concatedErrorText = implode(', ', $errorText);
        return "Amadeus Response Error -- $errorCode -- $concatedErrorText";
    }
}