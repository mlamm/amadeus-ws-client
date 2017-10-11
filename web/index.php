<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

set_time_limit(0);

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

$app->error(
    function (\Exception $ex, \Symfony\Component\HttpFoundation\Request $request, $code) {
        return new \Symfony\Component\HttpFoundation\JsonResponse(
            [
                'error' => [
                    '_' => [
                        [
                            'code' => 'ARS000X',
                            'message' => $ex->getMessage(),
                            'status' => $code
                        ]
                    ]
                ]
            ],
            $code
        );
    }
);

// register config
$app['config'] = $config= \Symfony\Component\Yaml\Yaml::parse(
    file_get_contents(getcwd() . '/config/app.yml'),
    \Symfony\Component\Yaml\Yaml::PARSE_OBJECT_FOR_MAP
);

$app['businesscase.search'] = $app->factory(function () use ($app) {

    $mapper = new Flight\Library\SearchRequest\ResponseMapping\Mapper(getcwd() . '/var/cache/response-mapping/');

    $responseTransformer = new AmadeusService\Search\Model\AmadeusResponseTransformer($mapper);
    $validator = new AmadeusService\Search\Request\Validator\AmadeusRequestValidator(
        $app['config']->search
    );

    return new AmadeusService\Search\BusinessCase\Search(
        $responseTransformer,
        $validator
    );
});

$app['amadeus.client'] = $app->factory(function() use ($app) {
    // IBE CACHE DATABASE SETUP
    $ibeCacheDatabaseConfig = new \Doctrine\DBAL\Configuration();

    $ibeCacheDatabaseConnectionParams = [
        'dbname' => $app['config']->search->database->ibe_cache->db_name,
        'user' => $app['config']->search->database->ibe_cache->user,
        'password' => $app['config']->search->database->ibe_cache->password,
        'host' => $app['config']->search->database->ibe_cache->host,
        'driver' => 'pdo_mysql'
    ];

    $ibeCacheDatabaseConnection = \Doctrine\DBAL\DriverManager::getConnection($ibeCacheDatabaseConnectionParams, $ibeCacheDatabaseConfig);

    return new \AmadeusService\Search\Model\AmadeusClient(
        $app['monolog'],
        $ibeCacheDatabaseConnection,
        $app['config']
    );

});

// application provider
$app->mount('/', new \AmadeusService\Index\IndexProvider());
$app->mount('/flight-search', new AmadeusService\Search\SearchProvider());

$app->run();
