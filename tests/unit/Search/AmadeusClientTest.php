<?php
namespace Flight\Service\Amadeus\Tests\Search;

use Amadeus\Client;
use Codeception\Test\Unit;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Service\Amadeus\Search\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Search\Model\AmadeusClient;
use Flight\Service\Amadeus\Search\Model\AmadeusRequestTransformer;
use Flight\Service\Amadeus\Search\Model\AmadeusResponseTransformer;
use Flight\Service\Amadeus\Search\Model\ClientParamsFactory;
use Flight\Service\Amadeus\Tests\Helper\RequestFaker;

/**
 * AmadeusClientTest.php
 *
 * test functionality of the class
 *
 * @covers Flight\Service\Amadeus\Search\Model\AmadeusClient
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusClientTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var AmadeusClient
     */
    private $object;

    /**
     * @var ClientParamsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientParamsFactory;

    /**
     * @var AmadeusRequestTransformer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestTransformer;

    /**
     * @var AmadeusResponseTransformer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseTransformer;

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    protected function _before()
    {
        $this->clientParamsFactory = $this->getMockBuilder(ClientParamsFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestTransformer = $this->getMockBuilder(AmadeusRequestTransformer::class)
            ->disableOriginalConstructor()->getMock();
        $this->responseTransformer = $this->getMockBuilder(AmadeusResponseTransformer::class)
            ->disableOriginalConstructor()->getMock();
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()->getMock();
        $clientBuilder = function (Client\Params $clientParams) {
            return $this->client;
        };

        $this->object = new AmadeusClient(
            $this->clientParamsFactory,
            $this->requestTransformer,
            $this->responseTransformer,
            $clientBuilder
        );
    }

    /**
     * Verify that the client is called with parameters from the request tranformer
     */
    public function testItCallsTheClient()
    {
        $request = RequestFaker::buildDefaultRequest();
        $businessCase = $request->getBusinessCases()->first()->first();
        $clientParams = new Client\Params();
        $requestOptions = new Client\RequestOptions\FareMasterPricerTbSearch();
        $amaResult = new Client\Result(new Client\Session\Handler\SendResult());
        $amaResult->status = Client\Result::STATUS_OK;
        $expecedSearchResponse = new SearchResponse();

        $this->clientParamsFactory
            ->expects($this->once())
            ->method('buildFromBusinessCase')
            ->with($businessCase)
            ->willReturn($clientParams);

        $this->requestTransformer
            ->expects($this->once())
            ->method('buildFareMasterRequestOptions')
            ->with($request)
            ->willReturn($requestOptions);

        $this->client
            ->expects($this->once())
            ->method('fareMasterPricerTravelBoardSearch')
            ->with($requestOptions)
            ->willReturn($amaResult);

        $this->responseTransformer
            ->expects($this->once())
            ->method('mapResultToDefinedStructure')
            ->with($businessCase, $amaResult)
            ->willReturn($expecedSearchResponse);

        $searchResponse = $this->object->search($request, $businessCase);
        $this->assertSame($expecedSearchResponse, $searchResponse);
    }

    /**
     * Verify that it throws an exception if the return status of the ama call is not OK
     */
    public function testItThrowsOnServiceError()
    {
        $request = RequestFaker::buildDefaultRequest();
        $businessCase = $request->getBusinessCases()->first()->first();
        $clientParams = new Client\Params();
        $requestOptions = new Client\RequestOptions\FareMasterPricerTbSearch();

        $amaResult = new Client\Result(new Client\Session\Handler\SendResult());
        $amaResult->status = Client\Result::STATUS_ERROR;
        $amaResult->messages = [
            new Client\Result\NotOk()
        ];

        $this->clientParamsFactory
            ->expects($this->any())
            ->method('buildFromBusinessCase')
            ->willReturn($clientParams);

        $this->requestTransformer
            ->expects($this->any())
            ->method('buildFareMasterRequestOptions')
            ->willReturn($requestOptions);

        $this->client
            ->expects($this->once())
            ->method('fareMasterPricerTravelBoardSearch')
            ->with($requestOptions)
            ->willReturn($amaResult);

        $this->expectException(AmadeusRequestException::class);
        $this->object->search($request, $businessCase);
    }
}
