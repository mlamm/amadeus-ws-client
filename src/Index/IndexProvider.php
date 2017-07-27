<?php
namespace AmadeusService\Index;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Index\BusinessCase\HealthCheck;
use Silex\ControllerCollection;

class IndexProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->get('/_hc', HealthCheck::class);
    }
}