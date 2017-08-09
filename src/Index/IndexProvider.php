<?php
namespace AmadeusService\Index;

use AmadeusService\Application\BusinessCaseProvider;
use AmadeusService\Index\BusinessCase\HealthCheck;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class IndexProvider
 * @package AmadeusService\Index
 */
class IndexProvider extends BusinessCaseProvider
{
    /**
     * @param ControllerCollection $collection
     */
    public function routing(ControllerCollection $collection)
    {
        $collection->match(
            '/',
            function () {
                return new RedirectResponse('/_hc');
            }
        );

        $collection->match('/_hc', HealthCheck::class);
    }
}