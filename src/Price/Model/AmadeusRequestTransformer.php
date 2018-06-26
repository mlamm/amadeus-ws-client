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
     * @var string
     */
    protected $customSessionHandlerClass;

    /**
     * AmadeusRequestTransformer constructor.
     *
     * @param \stdClass   $config
     * @param null|string $customPriceHandlerClass
     */
    public function __construct(\stdClass $config, $customPriceHandlerClass = null)
    {
        $this->config                    = $config;
        $this->customSessionHandlerClass = $customPriceHandlerClass;
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
                    'wsdl'              => './wsdl/' . $this->config->price->wsdl,
                    'logger'            => $logger,
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'service.Price',
                ],
            ]
        );

        if ($this->customSessionHandlerClass) {
            $clientParams->sessionHandler = new $this->customSessionHandlerClass($clientParams->sessionHandlerParams);
        }

        return $clientParams;
    }

    public function mapPostPriceResult($clientResult)
    {
        var_dump($clientResult);die(__METHOD__ . ':' . __LINE__); // %TODO
    }
}
