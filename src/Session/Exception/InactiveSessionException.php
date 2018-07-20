<?php
namespace Flight\Service\Amadeus\Session\Exception;

use Amadeus\Client\Result;
use Flight\Service\Amadeus\Application\Exception\ServiceException;

/**
 * Class InactiveSessionException
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class InactiveSessionException extends ServiceException
{
    /**
     * @var Result\NotOk[]
     */
    protected $errors = [];

    protected $internalErrorMessage = 'INACTIVE SESSION';

    const INTERNAL_ERROR_CODE = 'ARS0005';

    public function __construct(array $failures, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->failures = $failures;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorCode(): string
    {
        return 'ARS0005';
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage(): string
    {
        return $this->internalErrorMessage;
    }
}
