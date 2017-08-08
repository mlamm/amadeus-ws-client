<?php
namespace AmadeusService\Index;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Index\BusinessCase\HealthCheck;
use Silex\ControllerCollection;

/**
 * Class IndexProvider
 * @package AmadeusService\Index
 */
class IndexProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->match('/_hc', HealthCheck::class);
    }
}