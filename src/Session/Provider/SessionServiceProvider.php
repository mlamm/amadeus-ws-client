<?php

namespace Flight\Service\Amadeus\Session\Provider;

use Amadeus;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Session;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 * Register the session service and some of its dependencies in the app container
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class SessionServiceProvider implements ServiceProviderInterface
{

    /**
     * @var bool
     */
    private $useMockSearchResponse = false;

    /**
     * @param bool $useMockSearchResponse
     */
    public function __construct(bool $useMockSearchResponse)
    {
        $this->useMockSearchResponse = $useMockSearchResponse;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['service.session'] = function () use ($app) {
            $validator = new Session\Request\Validator\Session(
                $app['config']->session
            );

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir(__DIR__ . '/../../../var/cache/serializer');

            return new Session\Service\Session(
                $validator,
                $serializerBuilder->build(),
                $app['amadeus.client.session'],
                $app['config']->session
            );
        };
        $app['monolog.logfile'] = '/../var/logs/app.log';
        $app['amadeus.client.session'] = function () use ($app) {
            $sessionHandlerClass = $this->useMockSearchResponse ? MockSessionHandler::class : null;
            return new Session\Model\AmadeusClient(
                $app['config']->debug->remarks->log_ama_traffic ? $app['logger'] : new NullLogger(),
                new Session\Model\AmadeusRequestTransformer($app['config'], $sessionHandlerClass),
                new Session\Model\AmadeusResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };
    }
}