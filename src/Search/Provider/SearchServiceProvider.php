<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Provider;

use Amadeus;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Search\Model\AmadeusClient;
use Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer;
use Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer;
use Flight\Service\Amadeus\Search\Model\ClientParamsFactory;
use Flight\Service\Amadeus\Search\Request\Validator\AmadeusRequestValidator;
use Flight\Service\Amadeus\Search\Service\Search;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 * SearchServiceProvider.php
 *
 * Register the search service and some of its dependencies in the app container
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SearchServiceProvider implements ServiceProviderInterface
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

    public function register(Container $app)
    {
        $app['amadeus.client.search'] = function ($app) {

            $sessionHandlerClass = $this->useMockSearchResponse ? MockSessionHandler::class : null;
            $sessionLogger = $app['config']->debug->log_ama_traffic ? $app['logger'] : new NullLogger();

            $clientParamFactory = new ClientParamsFactory($app['config'], $sessionLogger, $sessionHandlerClass);

            return new AmadeusClient(
                $clientParamFactory,
                new AmadeusRequestTransformer($app['config']),
                new AmadeusResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };

        $app['service.search'] = function ($app) {
            $validator = new AmadeusRequestValidator(
                $app['config']->search
            );

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir('var/cache/serializer');

            return new Search(
                $validator,
                $serializerBuilder->build(),
                new Mapper(),
                $app['cache.flights'],
                $app['amadeus.client.search'],
                $app['config']->search
            );
        };
    }
}
