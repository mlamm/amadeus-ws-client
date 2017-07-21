<?php
namespace AmadeusService\Search\BusinessCase;

use AmadeusService\Service\BusinessCase;

class GetSearch extends BusinessCase
{
    protected function respond()
    {
        return $this->application()->json([]);
    }
}