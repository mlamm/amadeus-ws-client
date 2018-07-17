<?php
namespace Flight\Service\Amadeus\Itinerary;

/**
 * Class Itinerary Provider
 *
 * @package Flight\Service\Amadeus\Itinerary
 */
class ItineraryProvider extends \Flight\Service\Amadeus\Application\BusinessCaseProvider
{

    /**
     * Method to setup the routing for the endpoint.
     *
     * @inheritdoc
     */
    public function routing(\Silex\ControllerCollection $collection)
    {
        $collection->get('/', 'businesscase.itinerary-read');
    }
}
