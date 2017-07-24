<?php
namespace AmadeusService\Index;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Index\BusinessCase\Index;
use Silex\ControllerCollection;

class IndexProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->get('/', Index::class);
    }
}