<?php

namespace Flight\Service\Amadeus\Application\Provider;

use Flight\Service\Amadeus\Application\Logger\ErrorLogger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ErrorLoggerProvider
 *
 * @author    Falk Woelfing <falk.woelfing@invia.de>
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ErrorLoggerProvider implements ServiceProviderInterface
{

    /**
     * Register logger from application to error logger.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['error-logger'] = function ($app) {
            return new ErrorLogger($app['logger']);
        };
    }
}