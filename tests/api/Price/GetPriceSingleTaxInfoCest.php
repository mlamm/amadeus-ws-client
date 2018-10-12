<?php

use GuzzleHttp\Psr7\Request;

/**
 * Test the GET /price endpoint while using phiremock as the gds backend here.
 * The display-tst response xml has a single-element of *taxInformation* in it,
 * therefore some objects that are used to be an array are now \stdClasses.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class GetPriceSingleTaxInfoCest
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
            codecept_data_dir('fixtures/Ticket_DisplayTST-SingleTax.xml')
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
        $request = new Request('GET', 'http://amadeus-nginx/price?XDEBUG_SESSION_START=service-amadeus', $httpHeader);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());

        /** @var \GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();
        /** @var \stdClass $responseBody */
        $responseBody = \json_decode($responseBody);

        \PHPUnit_Framework_Assert::assertCount(2, $responseBody->passenger_price);
        \PHPUnit_Framework_Assert::assertSame(93, $responseBody->passenger_price[0]->equiv_fare);
        \PHPUnit_Framework_Assert::assertSame(15, $responseBody->passenger_price[0]->total_tax);
        \PHPUnit_Framework_Assert::assertSame(105, $responseBody->passenger_price[0]->base_fare);
        \PHPUnit_Framework_Assert::assertSame('CHF', $responseBody->passenger_price[0]->base_fare_currency);
        \PHPUnit_Framework_Assert::assertSame('EUR', $responseBody->passenger_price[0]->equiv_fare_currency);

        \PHPUnit_Framework_Assert::assertSame(70, $responseBody->passenger_price[1]->equiv_fare);
        \PHPUnit_Framework_Assert::assertSame(229.01, $responseBody->passenger_price[1]->total_tax);
        \PHPUnit_Framework_Assert::assertSame(79, $responseBody->passenger_price[1]->base_fare);
        \PHPUnit_Framework_Assert::assertSame('CHF', $responseBody->passenger_price[1]->base_fare_currency);
        \PHPUnit_Framework_Assert::assertSame('EUR', $responseBody->passenger_price[1]->equiv_fare_currency);

    }
}
