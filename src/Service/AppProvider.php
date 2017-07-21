<?php
namespace AmadeusService\Service;

use Silex\Api\ControllerProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

abstract class AppProvider implements ControllerProviderInterface, EventListenerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $this->routing($controllers);
        return $controllers;
    }

    abstract public function routing(ControllerCollection $controllers);
}