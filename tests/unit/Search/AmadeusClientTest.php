<?php
namespace Search;


use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusClient;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Psr\Log\LoggerInterface;

class AmadeusClientTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCreatingAnAmadeusClient()
    {
        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('log');

        $authentication = \Mockery::mock(BusinessCaseAuthentication::class);
        $authentication
            ->shouldReceive('getOfficeId')
            ->once();

        $authentication
            ->shouldReceive('getUserId')
            ->once();

        $authentication
            ->shouldReceive('getPasswordData')
            ->once();

        $authentication
            ->shouldReceive('getPasswordLength')
            ->once();

        $authentication
            ->shouldReceive('getDutyCode')
            ->once();

        $authentication
            ->shouldReceive('getOrganizationId')
            ->once();

        $businessCase = \Mockery::mock(BusinessCase::class);
        $businessCase
            ->shouldReceive('getAuthentication')
            ->times(6)
            ->andReturn($authentication);

        $amaClient = new AmadeusClient(
            $logger,
            $businessCase,
            __DIR__ . '/../../_support/fixtures/dummy.wsdl'
        );
        $client = $amaClient->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}