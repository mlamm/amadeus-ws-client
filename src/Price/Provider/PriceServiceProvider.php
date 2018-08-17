<?php

namespace Flight\Service\Amadeus\Price\Provider;

use Amadeus;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Price;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 * Register the Price service and some of its dependencies in the app container
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class PriceServiceProvider implements ServiceProviderInterface
{

    /**
     * @var bool
     */
    private $useMockPriceResponse = false;

    /**
     * @param bool $useMockPriceResponse
     */
    public function __construct(bool $useMockPriceResponse)
    {
        $this->useMockPriceResponse = $useMockPriceResponse;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app) : void
    {
        $app['service.price']        = function () use ($app) {
            $validator = new Price\Request\Validator\Price(
                $app['config']->price
            );

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir(__DIR__ . '/../../../var/cache/serializer');

            return new Price\Service\Price(
                $validator,
                $serializerBuilder->build(),
                $app['amadeus.client.Price']
            );
        };
        $app['monolog.logfile']      = '/../var/logs/app.log';
        $app['amadeus.client.Price'] = function () use ($app) {
            $sessionHandlerClass = $this->useMockPriceResponse ? MockSessionHandler::class : null;
            return new Price\Model\AmadeusClient(
                $app['config']->debug->price->log_ama_traffic ? $app['logger'] : new NullLogger(),
                new Price\Model\AmadeusRequestTransformer($app['config'], $sessionHandlerClass),
                new Price\Model\PriceResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };
    }
}
