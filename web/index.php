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

$app['service-container'] = function () {
    return new \Symfony\Component\DependencyInjection\ContainerBuilder();
};

// application provider
$app->mount('/', new \AmadeusService\Index\IndexProvider());
$app->mount('/search', new AmadeusService\Search\SearchProvider());

$app->run();