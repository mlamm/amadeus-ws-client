<?php
namespace Flight\Service\Amadeus\Price\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Particle\Validator\Failure;
use Throwable;

/**
 * Class RemarksRequestFailedException
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class InvalidRequestParameterException extends ServiceException
{
    /**
     * @var Failure[]
     */
    protected $failures;

    /**
     * InvalidRequestParameterException constructor.
     *
     * @param array          $failures
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(array $failures, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->failures = $failures;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorCode(): string
    {
        return 'ARS0002';
    }

    /**
     * @return Failure[]
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage(): string
    {
        return 'INVALID OR MISSING REQUEST PARAM';
    }
}
