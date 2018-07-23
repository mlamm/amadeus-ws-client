<?php

namespace Flight\Service\Amadeus\Metrics\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;

/**
 * Print metrics to endpoint.
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    t.sari <tibor.sari@invia.de>
 */
class PrometheusMetrics extends BusinessCase
{
    /**
     * Method that defines the response of a business case
     *
     * @return Response
     */
    public function respond()
    {
        $renderer = new RenderTextFormat();
        $result = $renderer->render($this->application['metrics.prometheus.registry']->getMetricFamilySamples());

        return new Response($result, Response::HTTP_OK, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}