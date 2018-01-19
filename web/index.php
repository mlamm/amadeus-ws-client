<?php

use Flight\Service\Amadeus\Application\Config\CachedConfig;
use Flight\Service\Amadeus\Application\Middleware\JsonEncodingOptions;
use Flight\Service\Amadeus\Application\Provider\ErrorProvider;
use Flight\Service\Amadeus\Search\Cache\CacheProvider;
use Flight\Service\Amadeus\Search\Provider\SearchServiceProvider;
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

$app = new Application();

$app->register(new ErrorProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new CacheProvider());

// switch to mock service responses for api tests
$useMockAmaResponses = env('MOCK_AMA_RESPONSE_IN_TEST', 'disabled') === 'enabled'
    && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Symfony BrowserKit';

$app->register(new SearchServiceProvider($useMockAmaResponses));

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

$config = $app['config'];

// set json ecoding options from config
$app->after(new JsonEncodingOptions($config));

// search
$app['businesscase.search'] = function () use ($app) {
    return new Flight\Service\Amadeus\Search\BusinessCase\Search(
        $app['service.search'],
        $app['monolog']
    );
};

$app['service.search'] = function () use ($app) {
    $validator = new Flight\Service\Amadeus\Search\Request\Validator\AmadeusRequestValidator(
        $app['config']->search
    );

    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
    $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

    $serializerBuilder->setCacheDir(__DIR__ . '/../var/cache/serializer');

    return new \Flight\Service\Amadeus\Search\Service\Search(
        $validator,
        $serializerBuilder->build(),
        $app['cache.flights'],
        $app['amadeus.client'],
        $app['config']->search,
        $app['monolog']
    );
};

$app['amadeus.client'] = function () use ($app) {
    return new \Flight\Service\Amadeus\Search\Model\AmadeusClient(
        $app['config'],
        $app['monolog'],
        new \Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer($app['config']),
        new \Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer(),
        function (Amadeus\Client\Params $clientParams) {
            return new Amadeus\Client($clientParams);
        }
    );
};

// remarks
$app['businesscase.remarks-read'] = function () use ($app) {
    return new Flight\Service\Amadeus\Remarks\BusinessCase\RemarksRead(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-add'] = function () use ($app) {
    return new Flight\Service\Amadeus\Remarks\BusinessCase\RemarksAdd(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-delete'] = function () use ($app) {
    return new Flight\Service\Amadeus\Remarks\BusinessCase\RemarksDelete(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['businesscase.remarks-modify'] = function () use ($app) {
    return new Flight\Service\Amadeus\Remarks\BusinessCase\RemarksModify(
        $app['service.remarks'],
        $app['monolog']
    );
};

$app['service.remarks'] = function () use ($app) {
    $validator = new Flight\Service\Amadeus\Remarks\Request\Validator\RemarksRead(
        $app['config']->remarks
    );

    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
    $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

    $serializerBuilder->setCacheDir(__DIR__ . '/../var/cache/serializer');

    return new \Flight\Service\Amadeus\Remarks\Service\Remarks(
        $validator,
        $serializerBuilder->build(),
        $app['cache.flights'],
        $app['remarksamadeus.client'],
        $app['config']->remarks
    );
};

$app['remarksamadeus.client'] = function () use ($app) {
    return new \Flight\Service\Amadeus\Remarks\Model\RemarksAmadeusClient(
        $app['config'],
        $app['monolog'],
        new \Flight\Service\Amadeus\Remarks\Model\AmadeusRequestTransformer($app['config']),
        new \Flight\Service\Amadeus\Remarks\Model\AmadeusResponseTransformer(),
        function (Amadeus\Client\Params $clientParams) {
            return new Amadeus\Client($clientParams);
        }
    );
};

$app->register(new \Flight\Service\Amadeus\Search\Cache\CacheProvider());

// application provider
$app->mount('/', new \Flight\Service\Amadeus\Index\IndexProvider());
$app->mount('/flight-search', new Flight\Service\Amadeus\Search\SearchProvider());

if ($config->debug->pimpledump->enabled) {
    $app->register(new \Sorien\Provider\PimpleDumpProvider(), [
        'pimpledump.output_dir' => __DIR__ . '/../var/logs',
    ]);
}

if ($config->debug->throwup->enabled) {
    $app->mount('/throwup', new \Flight\ThrowUp\SilexProvider());
}

$app->run();
