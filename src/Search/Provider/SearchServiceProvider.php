<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Provider;

use Amadeus;
use Flight\Service\Amadeus\Search\Request\Validator\AmadeusRequestValidator;
use Flight\Service\Amadeus\Search\Service\Search;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
    public function register(Container $app)
    {
        $app['amadeus.client'] = function ($app) {
            return new \Flight\Service\Amadeus\Search\Model\AmadeusClient(
                $app['config'],
                $app['monolog'],
                new \Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer($app['config']),
                new \Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer(),
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
                $app['cache.flights'],
                $app['amadeus.client'],
                $app['config']->search
            );
        };
    }
}
