<?php
namespace Flight\Service\Amadeus\Application\Exception;

/**
 * Class GeneralServerErrorException
 * @package Flight\Service\Amadeus\Application\Exception
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
