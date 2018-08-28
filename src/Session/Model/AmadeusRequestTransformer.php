<?php

namespace Flight\Service\Amadeus\Session\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Session\Request\Entity\Authenticate;
use Psr\Log\LoggerInterface;

/**
 * AmadeusRequestTransformer
 *
 * Build an Amadeus session request
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusRequestTransformer
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * @var string
     */
    protected $customSessionHandlerClass;

    /**
     * AmadeusRequestTransformer constructor.
     *
     * @param \stdClass   $config
     * @param null|string $customSessionHandlerClass
     */
    public function __construct(\stdClass $config, $customSessionHandlerClass = null)
    {
        $this->config                    = $config;
        $this->customSessionHandlerClass = $customSessionHandlerClass;
    }

    /**
     * builds the client
     *
     * @param Authenticate    $authentication
     * @param LoggerInterface $logger
     *
     * @return Client\Params
     */
    public function buildClientParams(Authenticate $authentication, LoggerInterface $logger) : Client\Params
    {
        $wsdlPath = './wsdl/' . $this->config->session->wsdl;

        $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
        if (!empty($this->config->price->overrideHost)) {
            $soapClient->__setLocation($this->config->price->overrideHost);
        }

        $clientParams = new Client\Params(
            [
                'authParams'           => [
                    'officeId'       => $authentication->getOfficeId(),
                    'userId'         => $authentication->getUserId(),
                    'passwordData'   => $authentication->getPasswordData(),
                    'passwordLength' => $authentication->getPasswordLength(),
                    'dutyCode'       => $authentication->getDutyCode(),
                    'organizationId' => $authentication->getOrganizationId(),
                ],
                'sessionHandlerParams' => [
                    'soapHeaderVersion' => Client::HEADER_V4,
                    'stateful'          => true,
                    'wsdl'              => $wsdlPath,
                    'logger'            => $logger,
                    'overrideSoapClient'         => $soapClient,
                    'overrideSoapClientWsdlName' => '16dbc24b' // %TODO
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'service.session',
                ],
            ]
        );

        if ($this->customSessionHandlerClass) {
            $clientParams->sessionHandler = new $this->customSessionHandlerClass($clientParams->sessionHandlerParams);
        }

        return $clientParams;
    }
}