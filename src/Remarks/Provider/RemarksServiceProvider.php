<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Provider;

use Amadeus;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Remarks;
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
class RemarksServiceProvider implements ServiceProviderInterface
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
        $app['service.remarks'] = function () use ($app) {
            $validator = new Remarks\Request\Validator\Remarks(
                $app['config']->remarks
            );

            \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
            $serializerBuilder = \JMS\Serializer\SerializerBuilder::create();

            $serializerBuilder->setCacheDir(__DIR__ . '/../var/cache/serializer');

            return new Remarks\Service\Remarks(
                $validator,
                $serializerBuilder->build(),
                $app['amadeus.client.remarks'],
                $app['config']->remarks
            );
        };
        $app['monolog.logfile'] = '/../var/logs/app.log';
        $app['amadeus.client.remarks'] = function () use ($app) {
            $sessionHandlerClass = $this->useMockSearchResponse ? MockSessionHandler::class : null;
            return new Remarks\Model\RemarksAmadeusClient(
                $app['config']->debug->remarks->log_ama_traffic ? $app['logger'] : new NullLogger(),
                new Remarks\Model\AmadeusRequestTransformer($app['config'], $sessionHandlerClass),
                new Remarks\Model\AmadeusResponseTransformer(),
                function (Amadeus\Client\Params $clientParams) {
                    return new Amadeus\Client($clientParams);
                }
            );
        };

    }
}
