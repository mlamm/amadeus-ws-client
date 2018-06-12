<?php

use Flight\Service\Amadeus\Application\Config\CachedConfig;
use Flight\Service\Amadeus\Application\Middleware\JsonEncodingOptions;
use Flight\Service\Amadeus\Application\Provider\ErrorProvider;
use Flight\Service\Amadeus\Search\Cache\CacheProvider;
use Flight\Service\Amadeus\Search\Provider\SearchServiceProvider;
use Flight\Service\Amadeus\Remarks;
use Flight\Service\Amadeus\Itinerary;
use Flight\TracingHeaderSilex\TracingHeaderProvider;
use Silex\Application;
use Symfony\Component\Yaml\Yaml;

// send all errors to the error handler
error_reporting(E_ALL);

// allow no plain text messages in the service response
ini_set('display_errors', 0);

// use php builtin error logging until our own error handler has been registered
ini_set('log_errors', 1);
ini_set('error_log', 'php://stdout');

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT_PATH', dirname(__DIR__));
$app = new Application();

// switch to mock service responses for api tests
$useMockAmaResponses = env('MOCK_AMA_RESPONSE_IN_TEST', 'disabled') === 'enabled'
    && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Symfony BrowserKit';

// register provider
$app->register(new ErrorProvider());
$app->register(new TracingHeaderProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new CacheProvider());
$app->register(new SearchServiceProvider($useMockAmaResponses));
$app->register(new Remarks\Provider\RemarksServiceProvider($useMockAmaResponses));
$app->register(new Itinerary\Provider\ItineraryServiceProvider($useMockAmaResponses));

// register config
$app['config'] = function () {
    return CachedConfig::load(
        env('CONFIG_CACHING', 'enabled') !== 'disabled',
        __DIR__ . '/../var/cache/config',
        function () {
            return Yaml::parse(
                file_get_contents(__DIR__ . '/../config/app.yml'),
                Yaml::PARSE_OBJECT_FOR_MAP
            );
        }
    );
};

// set json ecoding options from config
$app->after(new JsonEncodingOptions($app['config']));

// search cases
$app['businesscase.search'] = function () use ($app) {
    return new Flight\Service\Amadeus\Search\BusinessCase\Search(
        $app['service.search'],
        $app['error-logger']
    );
};

// remarks cases
$app['businesscase.remarks-read'] = function () use ($app) {
    return new Remarks\BusinessCase\RemarksRead(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-add'] = function () use ($app) {
    return new Remarks\BusinessCase\RemarksAdd(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-delete'] = function () use ($app) {
    return new Remarks\BusinessCase\RemarksDelete(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-modify'] = function () use ($app) {
    return new Remarks\BusinessCase\RemarksModify(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.itinerary-read'] = function() use ($app) {
    return new \Flight\Service\Amadeus\Itinerary\BusinessCase\ItineraryRead(
        $app['service.itinerary'],
        $app['monolog']
    );
};

$app->register(new \Flight\Service\Amadeus\Search\Cache\CacheProvider());

// application provider
$app->mount('/', new \Flight\Service\Amadeus\Index\IndexProvider());
$app->mount('/flight-search', new Flight\Service\Amadeus\Search\SearchProvider());
$app->mount('/remarks', new Remarks\RemarksProvider());
$app->mount('/itinerary', new Itinerary\ItineraryProvider());

if ($app['config']->debug->pimpledump->enabled) {
    $app->register(new \Sorien\Provider\PimpleDumpProvider(), [
        'pimpledump.output_dir' => __DIR__ . '/../var/logs',
    ]);
}

if ($app['config']->debug->throwup->enabled) {
    $app->mount('/throwup', new \Flight\ThrowUp\SilexProvider());
}

$app->run();
