<?php
namespace AmadeusService\Application\Exception;

/**
 * Interface ServiceExceptionInterface
 * @package AmadeusService\Application\Exception
 */
interface ServiceExceptionInterface
{
    /**
     * Method to return the internal error code
     * @return string
     */
    public function getInternalErrorCode();

    /**
     * Method to return the internal error message.
     * @return mixed
     */
    public function getInternalErrorMessage();
}