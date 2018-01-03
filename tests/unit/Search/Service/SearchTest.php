<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;
use Flight\Library\SearchRequest\ResponseMapping\Mapper;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Flight\Service\Amadeus\Search\Cache\FlightCacheInterface;
use Flight\Service\Amadeus\Search\Model\AmadeusClient;
use Flight\Service\Amadeus\Search\Request\Validator\AmadeusRequestValidator;
use Flight\Service\Amadeus\Search\Service\Search;
use Flight\Service\Amadeus\Tests\Helper\RequestFaker;
use JMS\Serializer\Serializer;

/**
 * SearchTest.php
 *
 * @covers \Flight\Service\Amadeus\Search\Service\Search
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
     * @var \PHPUnit_Framework_MockObject_MockObject|Mapper
     */
    private $mapper;

    /**
     * @var \stdClass
     */
    private $config;

    protected function _before()
    {
        $this->requestValidator = $this->getMockBuilder(AmadeusRequestValidator::class)
            ->disableOriginalConstructor()->getMock();
        $this->serializer       = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()->getMock();
        $this->cache            = $this->getMockBuilder(FlightCacheInterface::class)->getMock();
        $this->amadeusClient    = $this->getMockBuilder(AmadeusClient::class)
            ->disableOriginalConstructor()->getMock();
        $this->config           = new \stdClass();

        $this->mapper = $this->getMockBuilder(Mapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Search(
            $this->requestValidator,
            $this->serializer,
            $this->mapper,
            $this->cache,
            $this->amadeusClient,
            $this->config
        );
    }

    /**
     * @throws \Flight\Service\Amadeus\Search\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Search\Exception\InvalidRequestException
     * @throws \Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException
     */
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
        $searchResponse->setResult(new ArrayCollection());
        $expectedResponseJson = json_encode(['result' => []]);

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

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($this->anyThing(), $expectedResponseJson);

        $this->mapper
            ->expects($this->once())
            ->method('createJson')
            ->willReturnCallback(function ($a) {
                return json_encode($a);
            });

        $this->config->excluded_airlines = [];
        $this->config->request_options   = [];

        $response = $this->object->search($requestJson);
        $this->assertEquals($expectedResponseJson, $response);
    }
}
