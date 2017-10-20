<?php
namespace amadeusService\Tests\Search;

use Amadeus\Client;
use AmadeusService\Search\Exception\AmadeusRequestException;
use AmadeusService\Search\Exception\ServiceRequestAuthenticationFailedException;
use AmadeusService\Search\Model\AmadeusClient;
use AmadeusService\Search\Model\AmadeusRequestTransformer;
use AmadeusService\Search\Model\AmadeusResponseTransformer;
use AmadeusService\Tests\Helper\RequestFaker;
use Codeception\Test\Unit;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * AmadeusClientTest.php
 *
 * test functionality of the class
 *
 * @covers AmadeusService\Search\Model\AmadeusClient
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
     * @var \stdClass|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

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
        $this->config = new \stdClass();
        $this->logger = new NullLogger();
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
            $this->config,
            $this->logger,
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

        $this->requestTransformer
            ->expects($this->once())
            ->method('buildClientParams')
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

        $this->requestTransformer
            ->expects($this->any())
            ->method('buildClientParams')
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
