<?php
namespace AmadeusService\Search\Exception;

use Amadeus\Client;

/**
 * Class SearchException
 * @package AmadeusService\Search\Exception
 */
class SearchException extends \Exception
{
    const AMADEUS_AUTHENTICATION_REQUEST_FAILED = 'The `Amadeus\Client::securityAuthenticate` method didn\'t return state OK';
}