<?php
namespace AmadeusService\Search\BusinessCase;

use AmadeusService\Application\BusinessCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends BusinessCase
{
    public function responds()
    {
        return new JsonResponse();
    }
}