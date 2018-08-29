<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Psr\Log\LoggerInterface;

/**
 * ClientParamsFactory.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ClientParamsFactory
{
    /**
     * @var \stdClass
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $sessionLogger;

    /**
     * @param \stdClass       $config
     * @param LoggerInterface $sessionLogger
     */
    public function __construct(\stdClass $config, LoggerInterface $sessionLogger)
    {
        $this->config = $config;
        $this->sessionLogger = $sessionLogger;
    }

    /**
     * @param BusinessCase $businessCase
     * @return Client\Params
     */
    public function buildFromBusinessCase(BusinessCase $businessCase): Client\Params
    {
        $authentication = $businessCase->getAuthentication();
        $wsdlPath = './wsdl/' . $this->config->search->wsdl;
        $params = [
            'authParams' => [
                'officeId' => $authentication->getOfficeId(),
                'userId' => $authentication->getUserId(),
                'passwordData' => $authentication->getPasswordData(),
                'passwordLength' => $authentication->getPasswordLength(),
                'dutyCode' => $authentication->getDutyCode(),
                'organizationId' => $authentication->getOrganizationId()
            ],
            'sessionHandlerParams' => [
                'soapHeaderVersion' => Client::HEADER_V4,
                'stateful'          => false,
                'wsdl'              => $wsdlPath,
                'logger'            => $this->sessionLogger,
            ],
            'requestCreatorParams' => [
                'receivedFrom' => 'service.search'
            ]
        ];

        // override soap client for tests
        if (!empty($this->config->search->overrideHost)) {
            $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
            $soapClient->__setLocation($this->config->search->overrideHost);

            $params['sessionHandlerParams']['overrideSoapClient']         = $soapClient;
            $params['sessionHandlerParams']['overrideSoapClientWsdlName'] = '35a2ec45';
        }

        return new Client\Params($params);
    }
}
