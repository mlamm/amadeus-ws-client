<?php

use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use GuzzleHttp\Psr7\Request;

/**
 * Test the GET /itinerary endpoint while using phiremock as the gds backend here.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ItineraryReadValidRequestCest
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
            codecept_data_dir('fixtures/09-pnrRetrieve-response.xml')
        );
    }

    /**
     * Test the GET /itinerary endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetItin(ApiTester $I)
    {
        // http header, holding session and auth info
        $httpHeader = [
            'User-Agent' => 'Symfony BrowserKit',
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $client = new GuzzleHttp\Client();
        $request = new Request('GET', 'http://amadeus-nginx/itinerary/?recordLocator=QTDEOG', $httpHeader);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());

        /** @var \GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();
        /** @var \stdClass $responseBody */
        $responseBody = \json_decode($responseBody);

        \PHPUnit_Framework_Assert::assertEquals(json_decode(file_get_contents(
            codecept_data_dir('fixtures/response/itinerary/correct-response.json')
        )),
            $responseBody
        );
    }
}
