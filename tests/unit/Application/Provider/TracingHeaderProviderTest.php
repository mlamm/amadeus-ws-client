<?php

use Codeception\Util\Stub;
use Flight\Service\Amadeus\Application\Logger\TracingHeader;
use Flight\Service\Amadeus\Application\Provider\TracingHeaderProvider;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ErrorProviderTest.php
 *
 * testing TracingHeaderProvider
 *
 * @coversDefaultClass Flight\Service\Amadeus\Application\Provider\TracingHeaderProvider
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class TracingHeaderProviderTest extends \Codeception\Test\Unit
{
    /**
     * test registration tracing header provider in a container
     *
     * @covers ::register
     * @throws Exception
     */
    public function testRegisters()
    {
        $app = new \Silex\Application();

        /** @var RequestStack $requestStack */
        $requestStack = Stub::make(RequestStack::class);
        $requestStack->push(new \Symfony\Component\HttpFoundation\Request());

        $app->register(new TracingHeaderProvider(),
            [
                'request_stack' => $requestStack,
            ]
        );

        self::assertInstanceOf(TracingHeader::class, $app['tracing.header']);
    }
}

