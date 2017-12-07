<?php
namespace Flight\Service\Amadeus\Search\Traits;

use Flight\SearchRequestMapping\Entity\Request as FlightRequest;
use Flight\SearchRequestMapping\Mapper;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait SearchRequestMappingTrait
 * @package Flight\Service\Amadeus\Search\Traits
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
            throw new InvalidRequestException('', 0, $ex);
        }
    }
}
