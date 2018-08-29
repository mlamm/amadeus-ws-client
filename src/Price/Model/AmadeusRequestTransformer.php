<?php

namespace Flight\Service\Amadeus\Price\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Price\Request\Entity\Authenticate;
use Psr\Log\LoggerInterface;

/**
 * AmadeusRequestTransformer
 *
 * Build an Amadeus Price request
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
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
        $wsdlPath = './wsdl/' . $this->config->price->wsdl;
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
                'soapHeaderVersion'          => Client::HEADER_V4,
                'stateful'                   => true,
                'wsdl'                       => $wsdlPath,
                'logger'                     => $logger,
            ],
            'requestCreatorParams' => [
                'receivedFrom' => 'service.Price',
            ],
        ];

        // override soap client for tests
        if (!empty($this->config->price->overrideHost)) {
            $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
            $soapClient->__setLocation($this->config->price->overrideHost);

            $params['sessionHandlerParams']['overrideSoapClient']         = $soapClient;
            // wsdl-hash that is internally used to match the right soap-client
            $params['sessionHandlerParams']['overrideSoapClientWsdlName'] = '16dbc24b';
        }

        return new Client\Params($params);
    }
}
