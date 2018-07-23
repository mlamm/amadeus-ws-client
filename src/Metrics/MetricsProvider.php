<?php
namespace Flight\Service\Amadeus\Metrics;

use Flight\Service\Amadeus\Application\BusinessCaseProvider;
use Flight\Service\Amadeus\Metrics\BusinessCase\PrometheusMetrics;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Silex\Application;

/**
 * Class Metrics Provider
 *
 * @package Flight\Service\Ypsilon\Metrics
 */
class MetricsProvider extends BusinessCaseProvider implements ServiceProviderInterface
{
    public const CACHE_REQUEST_HIT = 'hit';
    public const CACHE_REQUEST_MISS = 'miss';

    /**
     * Method to setup the routing for the endpoint.
     *
     * @inheritdoc
     */
    public function routing(\Silex\ControllerCollection $collection)
    {
        $collection->match('/', PrometheusMetrics::class);
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container|Application $application
     */
    public function register(Container $application)
    {
        $registry = $this->registerMetrics($application);
        $application['metrics.prometheus.registry'] = $registry;
        $application['metrics.prometheus.tracker'] = function () use ($application) {
            return new MetricsTracker($application);
        };
        $application->mount('/metrics', new MetricsProvider);
    }

    /**
     * @param Container $application
     *
     * @return CollectorRegistry
     */
    private function registerMetrics(Container $application)
    {
        switch ($application['config']->prometheus->storage_type) {
            case 'apcu':
                $registry = new CollectorRegistry(new APC);
                break;

            case 'memory':
                $registry = $registry = new CollectorRegistry(new InMemory);
                break;

            default:
                $registry = CollectorRegistry::getDefault();
        }

        $application['metrics.supplier_response_time_seconds'] = function () use ($registry) {
            return $registry->getOrRegisterHistogram(
                'supplier',
                'response_time_seconds',
                'Time between the connection to 3rd party supplier and receiving the last byte of the response.',
                ['status', 'supplier_name', 'action'], // labels
                [1, 2, 3, 5, 8, 10, 30] // buckets
            );
        };

        $application['metrics.supplier_cache_requests_total'] = function () use ($registry) {
            return $registry->getOrRegisterCounter(
                'supplier',
                'cache_requests_total',
                'Request count for cache requests used to reduce supplier requests.',
                ['status', 'supplier_name', 'action'] // labels
            );
        };


        return $registry;
    }
}
