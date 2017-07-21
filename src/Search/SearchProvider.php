<?php
namespace AmadeusService\Search;

use AmadeusService\Search\BusinessCase\GetSearch;
use AmadeusService\Service\AppProvider;
use Pimple\Container;
use Silex\ControllerCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SearchProvider extends AppProvider
{
    public function routing(ControllerCollection $controllers)
    {
        $controllers->get('/search', GetSearch::class);
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
    }
}