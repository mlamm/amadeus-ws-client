<?php
namespace AmadeusService\Search\Traits;

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
        $mapper = new Mapper($request->getContent());
        return $mapper->getRequest();
    }
}
