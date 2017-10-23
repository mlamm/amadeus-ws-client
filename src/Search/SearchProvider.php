<?php
namespace Flight\Service\Amadeus\Search;

use Flight\Service\Amadeus\Application\BusinessCaseProvider;
use Silex\ControllerCollection;

class SearchProvider extends BusinessCaseProvider
{
    public function routing(ControllerCollection $collection)
    {
        $collection->match('/', 'businesscase.search');
    }    
}
