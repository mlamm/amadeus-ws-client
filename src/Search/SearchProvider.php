<?php
namespace AmadeusService\Search;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Search\BusinessCase\Search;
use Silex\ControllerCollection;

class SearchProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->post('/', Search::class);
    }    
}