<?php
namespace Search;

use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusClient;
use Doctrine\DBAL\Connection;
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
        /** @var LoggerInterface|\Mockery\MockInterface $logger */
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

        /** @var Connection|\Mockery\MockInterface $databaseMock */
        $databaseMock = \Mockery::mock(Connection::class);

        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->wsdl = '/../tests/_support/fixtures/dummy.wsdl';

        $amaClient = new AmadeusClient(
            $logger,
            $databaseMock,
            $config
        );
        $amaClient->prepare($businessCase);
        $client = $amaClient->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}
