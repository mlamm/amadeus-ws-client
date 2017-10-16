<?php
namespace amadeusService\Tests\Search;

use Amadeus\Client;
use AmadeusService\Search\Model\AmadeusClient;
use AmadeusService\Search\Model\AmadeusRequestTransformer;
use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Psr\Log\LoggerInterface;

/**
 * AmadeusClientTest.php
 *
 * test functionality of the class
 *
 * @coversDefaultClass AmadeusService\Search\Model\AmadeusClient
 *
 * @copyright Copyright (c) ${YEAR} Invia Flights Germany GmbH
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
     * @covers ::prepare
     */
    public function testCreatingAnAmadeusClient() : void
    {
        /** @var AmadeusRequestTransformer|\Mockery\MockInterface $requestTransformer **/
        $requestTransformer = \Mockery::mock(AmadeusRequestTransformer::class);
        $requestTransformer->shouldReceive('buildFareMasterRequestOptions')
            ->once();

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

        /** @var BusinessCase|\Mockery\MockInterface $businessCase */
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
            $requestTransformer,
            $logger,
            $databaseMock,
            $config
        );
        $amaClient->prepare($businessCase);
        $client = $amaClient->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}
