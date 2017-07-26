<?php
namespace AmadeusService\Search\BusinessCase;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Search\Model\AmadeusClient;
use AmadeusService\Search\Traits\SearchRequestMappingTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Search
 * @package AmadeusService\Search\BusinessCase
 */
class Search extends BusinessCase
{
    use SearchRequestMappingTrait;

    /**
     * @return JsonResponse
     */
    public function respond()
    {
        $request = $this->getMappedRequest($this->getRequest());

        $amadeusClient = new AmadeusClient(
            $this->getLogger(),
            $request->getBusinessCase()
        );

        return new JsonResponse(
            $amadeusClient->search($request)
        );
    }
}