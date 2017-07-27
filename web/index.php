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

// register a lazy DI container
$app['service-container'] = function () {
    return new \Symfony\Component\DependencyInjection\ContainerBuilder();
};

// register config
$app['config'] = \Symfony\Component\Yaml\Yaml::parse(
    file_get_contents(getcwd() . '/config/app.yml'),
    \Symfony\Component\Yaml\Yaml::PARSE_OBJECT_FOR_MAP
);

// application provider
$app->mount('/', new \AmadeusService\Index\IndexProvider());
$app->mount('/search', new AmadeusService\Search\SearchProvider());

$app->run();