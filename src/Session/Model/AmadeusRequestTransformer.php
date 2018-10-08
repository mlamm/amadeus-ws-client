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
     * AmadeusRequestTransformer constructor.
     *
     * @param \stdClass   $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
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
        $params = [
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
            ],
            'requestCreatorParams' => [
                'receivedFrom' => 'service.session',
            ],
        ];

        if (!empty($this->config->session->overrideHost)) {
            $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
            $soapClient->__setLocation($this->config->session->overrideHost);

            $params['sessionHandlerParams']['overrideSoapClient']         = $soapClient;
            // wsdl-hash that is internally used to match the right soap-client
            $params['sessionHandlerParams']['overrideSoapClientWsdlName'] = sprintf('%x', crc32($wsdlPath));
        }

        return new Client\Params($params);
    }
}