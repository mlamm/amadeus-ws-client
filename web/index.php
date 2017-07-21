<?php
chdir(__DIR__ . '/..');
require_once getcwd() . '/vendor/autoload.php';

$app = new Silex\Application();

// general service provider
$app->register(
    new Silex\Provider\MonologServiceProvider(), 
    [
        'monolog.logfile' => getcwd() . '/log/app.log'
    ]
);

// application provider
$app->mount('/search', new AmadeusService\Search\SearchProvider());

echo $app->run();