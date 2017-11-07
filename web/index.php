<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

set_time_limit(0);
ini_set('display_errors', 0);
ini_set('html_errors', 0);

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

// general service provider
$app->register(
    new Silex\Provider\MonologServiceProvider(),
    [
        'monolog.logfile' => getcwd() . '/var/logs/app.log',
        'monolog.formatter' => function () {
            return new \Monolog\Formatter\JsonFormatter();
        }
    ]
);

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->error(function (NotFoundHttpException $ex, Request $request, $code) {
    return new JsonResponse(
        [
            'errors' => [
                '_' => [
                    [
                        'code'    => 'ARS0404',
                        'message' => $ex->getMessage(),
                        'status'  => $code
                    ]
                ]
            ],
        ],
        $code
    );
});

$app->error(
    function (\Exception $ex, Request $request, $code) {
        return new JsonResponse(
            [
                'errors' => [
                    '_' => [
                        [
                            'code' => 'ARS000X',
                            'message' => 'SERVER ERROR - ' . $ex->getMessage(),
                            'status' => $code
                        ]
                    ]
                ]
            ],
            $code
        );
    }
);

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
$app['config'] = $config= \Symfony\Component\Yaml\Yaml::parse(
    file_get_contents(getcwd() . '/config/app.yml'),
    \Symfony\Component\Yaml\Yaml::PARSE_OBJECT_FOR_MAP
);

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
    $serializerBuilder->setCacheDir('var/cache/serializer');

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

$app->register(new \Flight\Service\Amadeus\Search\Cache\CacheProvider());

// application provider
$app->mount('/', new \Flight\Service\Amadeus\Index\IndexProvider());
$app->mount('/flight-search', new Flight\Service\Amadeus\Search\SearchProvider());

if ($config->debug->pimpledump->enabled) {
    $app->register(new \Sorien\Provider\PimpleDumpProvider(), [
        'pimpledump.output_dir' => __DIR__ . '/../var/logs',
    ]);
}

$app->run();
