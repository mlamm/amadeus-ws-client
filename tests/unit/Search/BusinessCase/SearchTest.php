<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\BusinessCase;

use Codeception\Test\Unit;
use Flight\Service\Amadeus\Application\Logger\ErrorLogger;
use Flight\Service\Amadeus\Search\BusinessCase\Search;
use Flight\Service\Amadeus\Search\Exception\InvalidRequestParameterException;
use Flight\Service\Amadeus\Search\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Search\Response\SearchResultResponse;
use Gamez\Psr\Log\TestLogger;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * SearchTest.php
 *
 * @covers Flight\Service\Amadeus\Search\BusinessCase\Search
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class SearchTest extends Unit
{
    /**
     * @var Search
     */
    private $object;

    /**
     * @var \Flight\Service\Amadeus\Search\Service\Search|\PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * @var TestLogger
     */
    private $logger;

    protected function _before()
    {
        $this->service = $this->getMockBuilder(\Flight\Service\Amadeus\Search\Service\Search::class)
            ->disableOriginalConstructor()->getMock();
        $this->logger = new TestLogger();

        $this->object = new Search($this->service, new ErrorLogger($this->logger));
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
