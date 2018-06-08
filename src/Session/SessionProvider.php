<?php
namespace Flight\Service\Amadeus\Session;

/**
 * Class SessionProvider
 * setup the routing for the session ndpoint.
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class SessionProvider extends \Flight\Service\Amadeus\Application\BusinessCaseProvider
{

    /**
     * Method to setup the routing for the endpoint.
     *
     * @inheritdoc
     */
    public function routing(\Silex\ControllerCollection $collection)
    {
        // here goes the route definition
        $collection->post('/create', 'businesscase.session-create');
    }
}
