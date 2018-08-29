<?php

use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use GuzzleHttp\Psr7\Request;

/**
 * Test POST /session/commit endpoint using phiremock for the gds backend.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class CommitSessionCest
{
    /**
     * @var \Helper\PhireHelper
     */
    private $phiremockHelper;

    /**
     * @param \Helper\PhireHelper $phiremockHelper
     */
    protected function _inject(\Helper\PhireHelper $phiremockHelper)
    {
        $this->phiremockHelper = $phiremockHelper;
    }

    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        $this->phiremockHelper->prep(
            $I,
            codecept_data_dir('fixtures/Security_Authenticate-Response.xml')
        );
    }

    /**
     * Test the POST /session/commit endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSessionIgnore(ApiTester $I)
    {
        // http header, holding session and auth info
        $httpHeader = [
            'User-Agent' => 'Symfony BrowserKit',
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $client = new GuzzleHttp\Client();
        $request = new Request('POST', 'http://amadeus-nginx/session/commit', $httpHeader);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(204, $response->getStatusCode());
    }
}
