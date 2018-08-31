<?php

use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use GuzzleHttp\Psr7\Request;

/**
 * Test POST /flight-search/ endpoint using phiremock for the gds backend.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ValidWithFiltersCest
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
        $this->phiremockHelper->prep($I,
            codecept_data_dir('fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt.xml'
        ));
    }

    /**
     * Test the POST /flight-search endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSearch(ApiTester $I)
    {
        $bodyRequestParams = file_get_contents(codecept_data_dir('requests/valid-request-with-filters.json'));

        $client = new GuzzleHttp\Client();
        $request = new Request(
            'POST',
            'http://amadeus-nginx/flight-search/',
            ['User-Agent' => 'Symfony BrowserKit'],
            $bodyRequestParams
        );

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());
        /** @var GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();
        $responseBody = \json_decode($responseBody);

        \PHPUnit_Framework_Assert::assertNotEmpty($responseBody);
        \PHPUnit_Framework_Assert::assertTrue(isset($responseBody->result));
    }
}
