<?php
namespace Flight\Service\Amadeus\Session;

/**
 * Class Session Provider
 *
 * @package Flight\Service\Amadeus\Session
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
        $collection->post('/ignore', 'businesscase.session-ignore');
        $collection->post('/terminate', 'businesscase.session-terminate');
    }


}
