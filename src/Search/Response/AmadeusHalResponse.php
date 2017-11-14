<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Response;

use Flight\Service\Amadeus\Application\Response\HalResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * AmadeusHalResponse.php
 *
 * Enforces the correct type for a HAL response
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusHalResponse extends HalResponse
{
    const CONTENT_TYPE = 'application/hal+json';

    public function __construct($data = null, $status = 200, array $headers = array(), $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
        $this->headers->set('Content-Type', self::CONTENT_TYPE);
    }

    public static function notFound($data) : self
    {
        return new static($data, Response::HTTP_NOT_FOUND);
    }

    public static function serverError($data) : self
    {
        return new static($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}