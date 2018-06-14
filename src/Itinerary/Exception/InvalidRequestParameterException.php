<?php

namespace Flight\Service\Amadeus\Itinerary\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Particle\Validator\Failure;
use Throwable;

/**
 * Class RemarksRequestFailedException
 *
 * @package Flight\Service\Amadeus\Remarks\Exception
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
    public function getInternalErrorCode()
    {
        return 'ARS0002';
    }

    /**
     * @return array|Failure[]
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * @inheritdoc
     */
    public function getInternalErrorMessage()
    {
        return 'INVALID OR MISSING REQUEST PARAM';
    }
}
