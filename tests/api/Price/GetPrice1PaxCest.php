<?php

use GuzzleHttp\Psr7\Request;

/**
 * Test the GET /price endpoint while using phiremock as the gds backend here.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class GetPrice1PaxCest
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
            codecept_data_dir('fixtures/Ticket_DisplayTST-1PAX.xml')
        );
    }

    /**
     * Test the GET /price endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetPrice(ApiTester $I)
    {
        // http header, holding session and auth info
        $httpHeader = [
            'User-Agent' => 'Symfony BrowserKit',
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $client = new GuzzleHttp\Client();
        $request = new Request('GET', 'http://amadeus-nginx/price/', $httpHeader);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());

        /** @var \GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();

        /** @var \stdClass $responseBody */
        $responseBody = \json_decode($responseBody);
        $expectedBody = file_get_contents(codecept_data_dir('fixtures/response/price/get-response-1-pax.json'));

        \PHPUnit_Framework_Assert::assertNotEmpty($expectedBody);
        \PHPUnit_Framework_Assert::assertEquals(\json_decode($expectedBody), $responseBody);
    }
}
