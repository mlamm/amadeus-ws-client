<?php
namespace Flight\Service\Amadeus\Session\Exception;

use Amadeus\Client\Result;
use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Class AmadeusRequestException
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusRequestException extends ServiceException
{
    /**
     * @var Result\NotOk[]
     */
    protected $errors = [];

    protected $internalErrorMessage = 'AMADEUS RESPONSE ERROR';

    /**
     * @param array $messages
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
    public function getInternalErrorCode(): string
    {
        return 'ARS0004';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage(): string
    {
        return $this->internalErrorMessage;
    }
}
