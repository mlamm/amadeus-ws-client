<?php
namespace AmadeusService\Search;

use AmadeusService\Application\BusinessCaseProvider;
use Silex\ControllerCollection;

class SearchProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->match('/', 'businesscase.search');
    }    
}
