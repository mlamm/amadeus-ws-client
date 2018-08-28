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
        $this->phiremockHelper->prep($I, MockSessionHandler::CREATE_SESSION_RESPONSE_FIXTURE);
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
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $client = new GuzzleHttp\Client();
        $request = new Request('POST', 'http://amadeus-nginx/session/commit', $httpHeader);

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());

        /** @var \GuzzleHttp\Psr7\Stream $responseBody */
        $responseBody = $response->getBody();
        $responseBody = $responseBody->getContents();
        /** @var \stdClass $responseBody */
        $responseBody = \json_decode($responseBody);

        \PHPUnit_Framework_Assert::assertSame('00BQC6J3LG', $responseBody->session_id);
        \PHPUnit_Framework_Assert::assertSame(1, $responseBody->sequence_number);
        \PHPUnit_Framework_Assert::assertSame('37OFZRPB5DUZSBH8BGUOSJ94V', $responseBody->security_token);
    }
}
