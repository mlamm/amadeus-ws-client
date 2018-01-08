<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Flight\Service\Amadeus\Search\Provider\ErrorProvider;
use Silex\Application;

set_time_limit(0);
ini_set('display_errors', 0);
ini_set('html_errors', 0);
error_reporting(E_ALL);

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$errorProvider = new ErrorProvider();
$errorProvider->registerHandlers();

$app->register($errorProvider);
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// set json ecoding options from config
$app->after(function (Request $request, Response $response) use ($app) {
    if ($response instanceof JsonResponse) {
        if (isset($app['config']->response->json_encoding_options)) {
            $value = 0;
            foreach ($app['config']->response->json_encoding_options as $option) {
                $value |= constant($option);
            }
            $response->setEncodingOptions($value);
        }
    }
});

// register config
$app['config'] = $config = \Symfony\Component\Yaml\Yaml::parse(
    file_get_contents(__DIR__ . '/../config/app.yml'),
    \Symfony\Component\Yaml\Yaml::PARSE_OBJECT_FOR_MAP
);

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
$app->mount('/remarks', new Flight\Service\Amadeus\Remarks\RemarksProvider());

if ($config->debug->pimpledump->enabled) {
    $app->register(new \Sorien\Provider\PimpleDumpProvider(), [
        'pimpledump.output_dir' => __DIR__ . '/../var/logs',
    ]);
}

if ($config->debug->throwup->enabled) {
    $app->mount('/throwup', new \Flight\ThrowUp\SilexProvider());
}

$app->run();
