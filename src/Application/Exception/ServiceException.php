<?php
namespace Flight\Service\Amadeus\Application\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceException
 * @package Flight\Service\Amadeus\Application\Exception
 */
abstract class ServiceException extends \Exception implements ServiceExceptionInterface, \JsonSerializable
{
    /**
     * @var integer
     */
    protected $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param int $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'code' => $this->getInternalErrorCode(),
            'message' => $this->getInternalErrorMessage(),
            'status' => $this->getResponseCode()
        ];
    }
}
