<?php

use Flight\Service\Amadeus\Application;
use Flight\Service\Amadeus\Remarks;
use Flight\Service\Amadeus\Itinerary;
use Flight\Service\Amadeus\Session;
use Flight\Service\Amadeus\Price;

// send all errors to the error handler
error_reporting(E_ALL);

// allow no plain text messages in the service response
ini_set('display_errors', 0);

// use php builtin error logging until our own error handler has been registered
ini_set('log_errors', 1);
ini_set('error_log', 'php://stdout');

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application;

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
$app['businesscase.session-create'] = function () use ($app) {
    return new Session\BusinessCase\CreateSession(
        $app['service.session'],
        $app['monolog']
    );
};
$app['businesscase.session-ignore'] = function () use ($app) {
    return new Session\BusinessCase\IgnoreSession(
        $app['service.session'],
        $app['monolog']
    );
};
$app['businesscase.session-terminate'] = function () use ($app) {
    return new Session\BusinessCase\TerminateSession(
        $app['service.session'],
        $app['monolog']
    );
};

$app['businesscase.session-commit'] = function () use ($app) {
    return new Session\BusinessCase\CommitSession(
        $app['service.session'],
        $app['monolog']
    );
};

$app['businesscase.itinerary-read'] = function() use ($app) {
    return new \Flight\Service\Amadeus\Itinerary\BusinessCase\ItineraryRead(
        $app['service.itinerary'],
        $app['monolog']
    );
};

$app['businesscase.price-delete'] = function() use ($app) {
    return new Price\BusinessCase\DeletePrice(
        $app['service.price'],
        $app['monolog']
    );
};

$app['businesscase.price-create'] = function() use ($app) {
    return new Price\BusinessCase\CreatePrice(
        $app['service.price'],
        $app['monolog']
    );
};

// application provider
$app->mount('/', new \Flight\Service\Amadeus\Index\IndexProvider());
$app->mount('/flight-search', new Flight\Service\Amadeus\Search\SearchProvider());
$app->mount('/remarks', new Remarks\RemarksProvider());
$app->mount('/session', new Session\SessionProvider());
$app->mount('/itinerary', new Itinerary\ItineraryProvider());
$app->mount('/price', new Price\PriceProvider());

if ($app['config']->debug->pimpledump->enabled) {
    $app->register(new \Sorien\Provider\PimpleDumpProvider(), [
        'pimpledump.output_dir' => __DIR__ . '/../var/logs',
    ]);
}

if ($app['config']->debug->throwup->enabled) {
    $app->mount('/throwup', new \Flight\ThrowUp\SilexProvider());
}

$app->run();

