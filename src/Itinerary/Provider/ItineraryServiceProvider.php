<?php

namespace Flight\Service\Amadeus\Itinerary\Provider;

use Amadeus;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Itinerary;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 * Register the itinerary service and some of its dependencies in the app container
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class ItineraryServiceProvider implements ServiceProviderInterface
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
     * register all that important stuff for the app
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['service.itinerary'] = function () use ($app) {
            $validator = new Itinerary\Request\Validator\Itinerary();

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir(__DIR__ . '/../var/cache/serializer');

            return new Itinerary\Service\ItineraryService(
                $validator,
                $serializerBuilder->build(),
                $app['amadeus.client.itinerary']
            );
        };
        $app['monolog.logfile'] = '/../var/logs/app.log';
        $app['amadeus.client.itinerary'] = function () use ($app) {
            $sessionHandlerClass = $this->useMockSearchResponse ? MockSessionHandler::class : null;
            return new Itinerary\Model\ItineraryAmadeusClient(
                $app['config']->debug->remarks->log_ama_traffic ? $app['logger'] : new NullLogger(),
                new Itinerary\Model\AmadeusRequestTransformer($app['config'], $sessionHandlerClass),
                new Itinerary\Model\AmadeusResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };
    }
}
