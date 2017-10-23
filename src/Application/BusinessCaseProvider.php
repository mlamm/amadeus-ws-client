<?php
namespace Flight\Service\Amadeus\Application;

use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BusinessCaseProvider implements ControllerProviderInterface, EventListenerProviderInterface, BusinessCaseProviderInterface
{
    /**
     * Method to connect the provider setup to the silex application
     *
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $collection = $app['controllers_factory'];
        $this->routing($collection);
        return $collection;
    }

    /**
     * Method to handle event listing for the current provider
     *
     * @param Container $app
     * @param EventDispatcherInterface $dispatcher
     * @return null
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        // empty
    }
}
