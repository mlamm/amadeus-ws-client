<?php
namespace Flight\Service\Amadeus\Search\Exception;

use Flight\Service\Amadeus\Application\Exception\ServiceException;
use Particle\Validator\Failure;
use Throwable;

/**
 * Class SearchRequestFailedException
 * @package Flight\Service\Amadeus\Search\Exception
 */
class InvalidRequestParameterException extends ServiceException
{
    /**
     * @var Failure[]
     */
    protected $failures;


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
