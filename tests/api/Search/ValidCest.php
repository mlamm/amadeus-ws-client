<?php

use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use GuzzleHttp\Psr7\Request;

/**
 * Test POST /flight-search/ endpoint using phiremock for the gds backend.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class ValidCest
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
        $this->phiremockHelper->prep($I, MockSessionHandler::MASTERPRICER_RESPONSE_FIXTURE);
    }

    /**
     * Test the POST /flight-search endpoint.
     *
     * @param ApiTester $I
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSearch(ApiTester $I)
    {
        $bodyRequestParams = file_get_contents(codecept_data_dir('requests/valid-request.json'));

        $client = new GuzzleHttp\Client();
        $request = new Request('POST', 'http://amadeus-nginx/flight-search/', [], $bodyRequestParams);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());
        /** @var GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();
        $responseBody = \json_decode($responseBody);
var_dump($responseBody);die(__METHOD__ . ':' . __LINE__); // %TODO
        \PHPUnit_Framework_Assert::assertNotEmpty($responseBody);
        \PHPUnit_Framework_Assert::assertTrue(isset($responseBody->result));
    }
}
