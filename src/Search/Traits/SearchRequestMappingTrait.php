<?php
namespace AmadeusService\Search\Traits;

use AmadeusService\Search\Exception\InvalidRequestException;
use Flight\SearchRequestMapping\Mapper;
use Symfony\Component\HttpFoundation\Request;
use \Flight\SearchRequestMapping\Entity\Request as FlightRequest;

/**
 * Trait SearchRequestMappingTrait
 * @package AmadeusService\Search\Traits
 */
trait SearchRequestMappingTrait
{
    /**
     * @param Request $request
     * @return FlightRequest
     * @throws InvalidRequestException
     */
    public function getMappedRequest(Request $request)
    {
        try {
            $mapper = new Mapper($request->getContent(), getcwd() . '/var/cache/request');
            return $mapper->getRequest();
        } catch (\Exception $ex) {
            throw new InvalidRequestException();
        }
    }
}
