<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\BusinessCase;

use AmadeusService\Search\BusinessCase\Search;
use AmadeusService\Search\Exception\InvalidRequestParameterException;
use AmadeusService\Search\Response\AmadeusErrorResponse;
use AmadeusService\Search\Response\SearchResultResponse;
use Gamez\Psr\Log\TestLogger;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * SearchTest.php
 *
 * @covers AmadeusService\Search\BusinessCase\Search
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
     * @var \AmadeusService\Search\Service\Search|\PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * @var TestLogger
     */
    private $logger;

    protected function _before()
    {
        $this->service = $this->getMockBuilder(\AmadeusService\Search\Service\Search::class)
            ->disableOriginalConstructor()->getMock();
        $this->logger = new TestLogger();
        $this->object = new Search($this->service, $this->logger);
    }

    /**
     * Verify that the correct response is returned for valid results
     */
    public function testItReturnsSearchResponse()
    {
        $requestData = json_encode(['request' => 'data']);
        $responseData = json_encode(['response' => 'data']);

        $this->service
            ->expects($this->once())
            ->method('search')
            ->with($requestData)
            ->willReturn($responseData);

        $response = $this->object->__invoke(new Application(), new Request([], [], [], [], [], [], $requestData));

        $this->assertInstanceOf(SearchResultResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(json_decode($responseData, true), json_decode($response->getContent(), true));
        $this->assertEmpty($this->logger->log);
    }

    /**
     * Verify that an error response is returned on input validation failure
     */
    public function testItReturnsBadRequestOnValidationException()
    {
        $requestData = json_encode(['request' => 'data']);

        $this->service
            ->expects($this->once())
            ->method('search')
            ->with($requestData)
            ->willThrowException(new InvalidRequestParameterException([]));

        $response = $this->object->__invoke(new Application(), new Request([], [], [], [], [], [], $requestData));

        $this->assertInstanceOf(AmadeusErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($this->logger->log);
    }

    /**
     * Verify that an error response is returned on exception from the service
     */
    public function testItReturnsServerErrorOnException()
    {
        $requestData = json_encode(['request' => 'data']);

        $this->service
            ->expects($this->once())
            ->method('search')
            ->with($requestData)
            ->willThrowException(new \Exception());

        $response = $this->object->__invoke(new Application(), new Request([], [], [], [], [], [], $requestData));

        $this->assertInstanceOf(AmadeusErrorResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertNotEmpty($this->logger->log);
    }
}
