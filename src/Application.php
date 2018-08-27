<?php

namespace Flight\Service\Amadeus;


use Flight\Service\Amadeus\Application\Config\CachedConfig;
use Flight\Service\Amadeus\Application\Middleware\JsonEncodingOptions;
use Flight\Service\Amadeus\Metrics\MetricsProvider;
use Flight\Service\Amadeus\Application\Provider\ErrorProvider;
use Flight\Service\Amadeus\Search\Cache\CacheProvider;
use Flight\Service\Amadeus\Search\Provider\SearchServiceProvider;
use Flight\TracingHeaderSilex\TracingHeaderProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\Yaml\Yaml;

/**
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    t.sari <tibor.sari@invia.de>
 */
class Application extends \Silex\Application
{

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // register config
        $this['config'] = function () {
            return CachedConfig::load(
                env('CONFIG_CACHING', 'enabled') !== 'disabled',
                __DIR__ . '/../var/cache/config',
                function () {
                    return Application::getConfig();
                }
            );
        };

        // set json encoding options from config
        $this->after(new JsonEncodingOptions($this['config']));

        // switch to mock service responses for api tests
        $useMockAmaResponses = env('MOCK_AMA_RESPONSE_IN_TEST', 'disabled') === 'enabled'
                               && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Symfony BrowserKit';

        // register provider
        $this->register(new ErrorProvider());
        $this->register(new TracingHeaderProvider());
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new CacheProvider());
        $this->register(new SearchServiceProvider($useMockAmaResponses));
        $this->register(new Remarks\Provider\RemarksServiceProvider($useMockAmaResponses));
        $this->register(new Session\Provider\SessionServiceProvider($useMockAmaResponses));
        $this->register(new Itinerary\Provider\ItineraryServiceProvider($useMockAmaResponses));
        $this->register(new Price\Provider\PriceServiceProvider($useMockAmaResponses));
        $this->register(new MetricsProvider);
    }

    /**
     * Build and return current config.
     *
     * @return mixed
     */
    public static function getConfig()
    {
        $configFile = 'app.yml';

        if (strpos($_SERVER['SCRIPT_FILENAME'], 'codecept')
            || (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Symfony BrowserKit')
        ) {
            $configFile = 'testing.yml';
        }

        return Yaml::parse(
            file_get_contents(__DIR__ . '/../config/' . $configFile),
            Yaml::PARSE_OBJECT_FOR_MAP
        );
    }
}