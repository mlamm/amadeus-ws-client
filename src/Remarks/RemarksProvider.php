<?php
namespace Flight\Service\Amadeus\Remarks;

/**
 * Class Remarks Provider
 *
 * @package Flight\Service\Amadeus\Remarks
 */
class RemarksProvider extends \Flight\Service\Amadeus\Application\BusinessCaseProvider
{

    /**
     * Method to setup the routing for the endpoint.
     *
     * @inheritdoc
     */
    public function routing(\Silex\ControllerCollection $collection)
    {
        $collection->get('/', 'businesscase.remarks-read');
        $collection->post('/', 'businesscase.remarks-add');
        $collection->delete('/', 'businesscase.remarks-delete');
    }
}
