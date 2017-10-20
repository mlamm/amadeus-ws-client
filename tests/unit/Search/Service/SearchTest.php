<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Service;

use AmadeusService\Search\Cache\FlightCacheInterface;
use AmadeusService\Search\Model\AmadeusClient;
use AmadeusService\Search\Request\Validator\AmadeusRequestValidator;
use AmadeusService\Search\Service\Search;
use AmadeusService\Tests\Helper\RequestFaker;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use JMS\Serializer\Serializer;
use Psr\Log\NullLogger;

/**
 * SearchTest.php
 *
 * @covers AmadeusService\Search\Service\Search
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SearchTest extends \Codeception\Test\Unit
{
    /**
     * @var Search
     */
    private $object;

    /**
     * @var AmadeusRequestValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestValidator;

    /**
     * @var Serializer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var FlightCacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var AmadeusClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $amadeusClient;

    /**
     * @var \stdClass
     */
    private $config;

    protected function _before()
    {
        $this->requestValidator = $this->getMockBuilder(AmadeusRequestValidator::class)
            ->disableOriginalConstructor()->getMock();
        $this->serializer = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()->getMock();
        $this->cache = $this->createMock(FlightCacheInterface::class);
        $this->amadeusClient = $this->getMockBuilder(AmadeusClient::class)
            ->disableOriginalConstructor()->getMock();
        $this->config = new \stdClass();

        $this->object = new Search(
            $this->requestValidator,
            $this->serializer,
            $this->cache,
            $this->amadeusClient,
            $this->config,
            new NullLogger()
        );
    }

    public function testItCallsAllTheThings()
    {
        $requestJson = '';

        $searchRequest = RequestFaker::buildDefaultRequest();
        $searchRequest->setFilterCabinClass([]);
        /** @var BusinessCase $businessCase */
        $businessCase = $searchRequest->getBusinessCases()->first()->first();
        $businessCase->setAuthentication(new BusinessCaseAuthentication());
        $businessCase->getAuthentication()->setOfficeId('office-id');

        $searchResponse = new SearchResponse();
        $expectedResponseJson = '';

        $this->requestValidator
            ->expects($this->once())
            ->method('validateRequest')
            ->with($requestJson);

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($searchRequest);

        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->amadeusClient
            ->expects($this->once())
            ->method('search')
            ->with($searchRequest, $searchRequest->getBusinessCases()->first()->first())
            ->willReturn($searchResponse);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($searchResponse)
            ->willReturn($expectedResponseJson);

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($this->anyThing(), $expectedResponseJson);

        $this->config->excluded_airlines = [];
        $this->config->request_options = [];

        $response = $this->object->search($requestJson);
        $this->assertEquals($expectedResponseJson, $response);
    }
}
