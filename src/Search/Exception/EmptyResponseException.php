<?php

namespace Flight\Service\Amadeus\Search\Exception;

/**
 * There was an amadeus error but we want to deliver an http 200 with empty result instead.
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    t.sari <tibor.sari@invia.de>
 */
class EmptyResponseException extends AmadeusRequestException
{

}