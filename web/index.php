<?php
chdir(__DIR__ . '/..');
require_once getcwd() . '/vendor/autoload.php';

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

// register a lazy DI container
$app['service-container'] = function () use ($config) {
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder */
    $containerBuilder = new \Symfony\Component\DependencyInjection\ContainerBuilder();

    // IBE DATABASE SETUP
    $ibeDatabaseConfig = new \Doctrine\DBAL\Configuration();

    $ibeDatabaseConnectionParams = [
        'dbname' => $config->search->database->ibe->db_name,
        'user' => $config->search->database->ibe->user,
        'password' => $config->search->database->ibe->password,
        'host' => $config->search->database->ibe->host,
        'driver' => 'pdo_mysql'
    ];

    $containerBuilder
        ->set(
            'database.ibe',
            \Doctrine\DBAL\DriverManager::getConnection($ibeDatabaseConnectionParams, $ibeDatabaseConfig)
        );

    // register your services
    return $containerBuilder;
};

// application provider
$app->mount('/', new \AmadeusService\Index\IndexProvider());
$app->mount('/flight-search', new AmadeusService\Search\SearchProvider());

$app->run();