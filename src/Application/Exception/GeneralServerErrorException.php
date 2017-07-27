<?php
namespace AmadeusService\Application\Exception;

/**
 * Class GeneralServerErrorException
 * @package AmadeusService\Application\Exception
 */
class GeneralServerErrorException extends ServiceException
{
    /**
     * @return string
     */
    public function getInternalErrorCode()
    {
        return 'ARS000X';
    }

    /**
     * @return string
     */
    public function getInternalErrorMessage()
    {
        return $this->getMessage();
    }
}