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
     * @var string|null
     */
    private $customSessionHandlerClass;

    /**
     * @param \stdClass       $config
     * @param LoggerInterface $sessionLogger
     * @param null|string     $customSessionHandlerClass
     */
    public function __construct(\stdClass $config, LoggerInterface $sessionLogger, $customSessionHandlerClass = null)
    {
        $this->config = $config;
        $this->sessionLogger = $sessionLogger;
        $this->customSessionHandlerClass = $customSessionHandlerClass;
    }

    /**
     * @param BusinessCase $businessCase
     * @return Client\Params
     */
    public function buildFromBusinessCase(BusinessCase $businessCase): Client\Params
    {
        $authentication = $businessCase->getAuthentication();

        $wsdlPath = './wsdl/' . $this->config->search->wsdl;
        $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
        if (!empty($this->config->price->overrideHost)) {
            $soapClient->__setLocation($this->config->price->overrideHost);
        }

        $params = new Client\Params(
            [
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
//                    'overrideSoapClient'         => $soapClient,
//                    'overrideSoapClientWsdlName' => '35a2ec45' // %TODO
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'service.search'
                ]
            ]
        );

        if ($this->customSessionHandlerClass) {
            $params->sessionHandler = new $this->customSessionHandlerClass($params->sessionHandlerParams);
        }

        return $params;}
}
