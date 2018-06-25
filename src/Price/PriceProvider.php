<?php
namespace Flight\Service\Amadeus\Price;

/**
 * Class PriceProvider
 * setup the routing for the Price endpoint.
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class PriceProvider extends \Flight\Service\Amadeus\Application\BusinessCaseProvider
{

    /**
     * Method to setup the routing for the endpoint.
     *
     * @inheritdoc
     */
    public function routing(\Silex\ControllerCollection $collection)
    {
        // here goes the route definition
        $collection->match('/', 'businesscase.price-delete')->method('DELETE');
    }
}
