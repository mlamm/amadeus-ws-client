<?php

namespace Helper;

use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;

/**
 * Phiremock helper class.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class PhireHelper extends Api
{
    /**
     * Prepare phiremock server with request.
     *
     * @param \ApiTester $I
     * @param $xmlResponseFile
     */
    public function prep(\ApiTester $I, $xmlResponseFile)
    {
        $config = \Flight\Service\Amadeus\Application::getConfig();

        \PHPUnit_Framework_Assert::assertFileExists($xmlResponseFile);
        \PHPUnit_Framework_Assert::assertFileIsReadable($xmlResponseFile);

        $phiremock = new Phiremock($config->phiremock->host, $config->phiremock->port);
        $phiremock->clearExpectations();

        $expectation = Phiremock::on(
            A::postRequest()->andUrl(Is::equalTo('/'))
        )->then(
            Respond::withStatusCode(200)
                ->andHeader('Content-Type', 'text/xml')
                ->andBody(file_get_contents($xmlResponseFile))
        );
        $phiremock->createExpectation($expectation);
    }

    /**
     * Return last request that ended up on the phiremock server.
     *
     * @return \stdClass
     */
    public function getLastRequest(): \stdClass
    {
        $config = \Flight\Service\Amadeus\Application::getConfig();
        $phiremock = new Phiremock($config->phiremock->host, $config->phiremock->port);

        $actualExecutions = $phiremock->listExecutions(
            A::postRequest()->andUrl(Is::equalTo('/'))
        );

        return end($actualExecutions);
    }
}