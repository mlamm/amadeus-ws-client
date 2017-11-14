<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Provider;

use Codeception\Test\Unit;
use Flight\Service\Amadeus\Search\Provider\ErrorProvider;
use Silex\Application;

/**
 * ErrorProviderTest.php
 *
 * testing ErrorProvider
 *
 * @coversDefaultClass Flight\Service\Amadeus\Search\Provider\ErrorProvider
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ErrorProviderTest extends Unit
{
    /**
     * test logger registration in a container
     *
     * @covers ::register
     */
    public function testItRegistersLogger()
    {
        $app = new Application();

        $errorProvider = new ErrorProvider();
        $errorProvider->register($app);

        $this->assertArrayHasKey('monolog.logfile', $app);
        $this->assertArrayHasKey('monolog.exception.logger.filter', $app);
    }
}