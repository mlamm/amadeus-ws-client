<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Application\Amadeus\Client;

use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseAuthentication;
use Flight\Service\Amadeus\Amadeus\Client\MockSessionHandler;
use Flight\Service\Amadeus\Search\Model\ClientParamsFactory;
use Psr\Log\NullLogger;

/**
 * ClientParamsFactoryTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\ClientParamsFactory
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ClientParamsFactoryTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it creates the client params from the given business case
     */
    public function testItBuildsClientParams()
    {
        $config = new \stdClass();
        $config->search = new \stdClass();
        $config->search->wsdl = 'wsdl';

        $businessCase = new BusinessCase();
        $businessCase->setAuthentication(new BusinessCaseAuthentication());
        $businessCase->getAuthentication()->setDutyCode('duty-code');
        $businessCase->getAuthentication()->setOfficeId('office-id');
        $businessCase->getAuthentication()->setOrganizationId('organization-id');
        $businessCase->getAuthentication()->setPasswordData('password-data');
        $businessCase->getAuthentication()->setPasswordLength('password-length');
        $businessCase->getAuthentication()->setUserId('user-id');

        $factory = new ClientParamsFactory($config, new NullLogger());
        $params = $factory->buildFromBusinessCase($businessCase);

        $this->assertEquals('duty-code', $params->authParams->dutyCode);
        $this->assertEquals('office-id', $params->authParams->officeId);
        $this->assertEquals('organization-id', $params->authParams->organizationId);
        $this->assertEquals('password-data', $params->authParams->passwordData);
        $this->assertEquals('password-length', $params->authParams->passwordLength);
        $this->assertEquals('user-id', $params->authParams->userId);
    }
}
