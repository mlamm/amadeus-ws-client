<?php

use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use GuzzleHttp\Psr7\Request;

/**
 * Test POST /price endpoint using phiremock for the gds backend.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class DeletePriceCest
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
            codecept_data_dir('fixtures/12-Ticket_DeleteTST-Response.xml')
        );
    }

    /**
     * Test the DELETE /price endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testPriceRequestForCorporateId(ApiTester $I)
    {
        // http header, holding session and auth info
        $httpHeader = [
            'User-Agent' => 'Symfony BrowserKit',
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $req = file_get_contents(codecept_data_dir('requests/Price/create-price-tarif.json'));
        $client = new GuzzleHttp\Client();
        $request = new Request('DELETE', 'http://amadeus-nginx/price/', $httpHeader, $req);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(204, $response->getStatusCode());
    }
}
