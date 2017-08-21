<?php
namespace AmadeusService\Search\Traits;

use AmadeusService\Search\Exception\InvalidRequestException;
use AmadeusService\Search\Exception\MissingRequestParameterException;
use Flight\SearchRequestMapping\Mapper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait SearchRequestMappingTrait
 * @package AmadeusService\Search\Traits
 */
trait SearchRequestMappingTrait
{
    /**
     * @param Request $request
     * @return \Flight\SearchRequestMapping\Entity\Request
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
