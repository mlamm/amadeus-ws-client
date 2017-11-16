<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * Error.php
 *
 * Represents a error on property
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Error
{
    const RESOURCE_NOT_FOUND = 'ARS0404';
    const SERVER_ERROR       = 'ARS0500';

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * Error constructor.
     *
     * @param string $property
     * @param string $code
     * @param int $status
     * @param string $message
     */
    public function __construct(string $property, string $code, int $status, string $message = '')
    {
        $this->property = $property;
        $this->code     = $code;
        $this->status   = $status;
        $this->message  = $message;
    }

    public static function resourceNotFound(\Throwable $exception) : self
    {
        return new static('_', self::RESOURCE_NOT_FOUND, 404, $exception->getMessage());
    }

    public static function serverError() : self
    {
        return new static('_', self::SERVER_ERROR, 500, Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
    }

    /**
     * the getter function for the property <em>$property</em>.
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * the getter function for the property <em>$code</em>.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * the getter function for the property <em>$status</em>.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * the getter function for the property <em>$message</em>.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
