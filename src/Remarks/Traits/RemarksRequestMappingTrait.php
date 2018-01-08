<?php
namespace Flight\Service\Amadeus\Remarks\Traits;

use Flight\Service\Amadeus\Remarks\Exception\InvalidRequestException;
use Flight\RemarksRequestMapping\Entity\Request as FlightRequest;
use Flight\RemarksRequestMapping\Mapper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RemarksRequestMappingTrait
 * @package Flight\Service\Amadeus\Remarks\Traits
 */
trait RemarksRequestMappingTrait
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
