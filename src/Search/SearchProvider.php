<?php
namespace AmadeusService\Search;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Search\BusinessCase\Index;
use Silex\ControllerCollection;

class SearchProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->get('/', Index::class);
    }    
}