<?php

use GuzzleHttp\Psr7\Request;

class GetPriceCest
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
        /**
         *
         *

        $I = new ApiTester($scenario);
        $I->wantTo('see an response that matches the defined schema if the request I send is valid');
        $I->haveHttpHeader(
            'session',
            file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json'))
        );
        $I->haveHttpHeader(
            'authentication',
            file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        );
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/price/');
        $I->seeResponseCodeIs(200);
        $I->seeHttpHeader('content-type', 'application/hal+json');
        $I->seeResponseIsHal();
        $I->seeResponseHasLinkToSelf('/price/');
        $I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/price/get-response-schema.json'));
         */


        $this->phiremockHelper = $phiremockHelper;
    }

    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        $this->phiremockHelper->prep($I, 'tests/_data/fixtures/lel.xml');
    }

    /**
     * Test the /price endpoint using corporate-id in the request params.
     *
     * @param ApiTester $I
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriceRequestForCorporateId(ApiTester $I)
    {
        // http header, holding session and auth info
        $httpHeader = [
            'session' => file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json')),
            'authentication' => file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
        ];

        $client = new GuzzleHttp\Client();
        $request = new Request('GET', 'http://amadeus-nginx/price/', $httpHeader, $this->getRequest());

        $response = $client->send($request);
        \PHPUnit_Framework_Assert::assertSame(200, $response->getStatusCode());

        $lastRequest = $this->phiremockHelper->getLastRequest();

        var_dump($lastRequest);die(__METHOD__ . ':' . __LINE__); // %TODO
//
//        $xmlRequestBody = $lastRequest->body;
//        $xmlRequestBody = simplexml_load_string($xmlRequestBody);
//
//        \PHPUnit_Framework_Assert::assertInstanceOf(\SimpleXMLElement::class, $xmlRequestBody);
//        $xmlRequestBody->registerXPathNamespace('ns1', 'http://webservices.sabre.com/sabreXML/2011/10');
//        $corporateNode = $xmlRequestBody->xpath(
//            '//ns1:PriceRequestInformation/ns1:OptionalQualifiers/ns1:PricingQualifiers/ns1:Corporate/ns1:ID/text()'
//        );
//
//        \PHPUnit_Framework_Assert::assertSame('AER05', (string)$corporateNode[0]);
    }

    /**
     * Get body request params with branded-fares included.
     *
     * @return string
     */
    protected function getRequest(): string
    {
        return json_encode([
            'tarif' => [
                'excursion' => true,
                'public' => true,
                'private' => true,
                'net' => false
            ],
            'passenger-types' => [
                'ADT' => 5
            ],
            'corporate-id' => 'AER05'
        ]);
    }
}
