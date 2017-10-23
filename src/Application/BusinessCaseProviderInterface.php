<?php
namespace Flight\Service\Amadeus\Application;

use Silex\ControllerCollection;

interface BusinessCaseProviderInterface
{
    /**
     * Method to setup the routing for the current provider
     *
     * @param ControllerCollection $collection
     * @return null
     */
    public function routing(ControllerCollection $collection);
}
