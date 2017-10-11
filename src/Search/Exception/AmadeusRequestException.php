<?php
namespace AmadeusService\Search\Exception;

use Amadeus\Client\Result;
use AmadeusService\Application\Exception\ServiceException;

/**
 * Class AmadeusRequestException
 * @package AmadeusService\Search\Exception
 */
class AmadeusRequestException extends ServiceException
{
    /**
     * @var Result\NotOk[]
     */
    protected $errors = [];

    protected $internalErrorMessage = 'AMADEUS RESPONSE ERROR';

    /**
     * @param \stdClass $error
     */
    public function __construct(array $messages)
    {
        foreach ($messages as $error) {
            if ($error instanceof Result\NotOk) {
                $this->errors[] = $error;
            }
            $this->internalErrorMessage .= " [{$error->code},{$error->text}],";
        }

        $this->internalErrorMessage = rtrim($this->internalErrorMessage, ',');

        parent::__construct($this->getInternalErrorMessage());
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
        return $this->internalErrorMessage;
    }
}
