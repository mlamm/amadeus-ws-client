<?php

namespace Flight\Service\Amadeus\Application\Provider;

use Flight\Service\Amadeus\Application\Logger\TracingHeader;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

/**
 * TracingHeaderProvider.php
 *
 * Register tracing header service
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class TracingHeaderProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     *
     * @return $this
     */
    public function register(Container $pimple)
    {
        $pimple['tracing.header'] = function (Application $application) {
            return new TracingHeader($application['request_stack']->getCurrentRequest()->headers->get(TracingHeader::TRACING_HEADER_NAME));
        };

        return $this;
    }
}