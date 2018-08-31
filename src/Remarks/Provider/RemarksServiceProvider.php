<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Provider;

use Amadeus;
use Flight\Service\Amadeus\Remarks;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 *
 * Register the remarks service and some of its dependencies in the app container
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class RemarksServiceProvider implements ServiceProviderInterface
{
    /**
     * @var bool
     */
    private $useMockSearchResponse = false;

    /**
     * register all that important stuff for the app
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['service.remarks'] = function () use ($app) {
            $validator = new Remarks\Request\Validator\Remarks(
                $app['config']->remarks
            );

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir(__DIR__ . '/../../../var/cache/serializer');

            return new Remarks\Service\Remarks(
                $validator,
                $serializerBuilder->build(),
                $app['amadeus.client.remarks'],
                $app['config']->remarks
            );
        };
        $app['monolog.logfile'] = '/../var/logs/app.log';
        $app['amadeus.client.remarks'] = function () use ($app) {
            return new Remarks\Model\RemarksAmadeusClient(
                $app['config']->debug->remarks->log_ama_traffic ? $app['logger'] : new NullLogger(),
                new Remarks\Model\AmadeusRequestTransformer($app['config']),
                new Remarks\Model\AmadeusResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };

    }
}
