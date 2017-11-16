<?php
namespace Flight\Service\Amadeus\Index;

use Flight\Service\Amadeus\Application\BusinessCaseProvider;
use Flight\Service\Amadeus\Index\BusinessCase\HealthCheck;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexProvider
 * @package Flight\Service\Amadeus\Index
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
                return new RedirectResponse('/health');
            }
        );

        $collection->match('/health', HealthCheck::class);

        $collection->match(
            '/docs',
            function () {
                return new Response(file_get_contents(__DIR__ . '/../../var/docs/index.html'));
            }
        );
    }
}
