<?php
declare(strict_types = 1);

namespace Flight\Service\Amadeus\Itinerary\Model;

use Amadeus\Client;
use Flight\Service\Amadeus\Itinerary\Request\Entity\Authenticate;
use Psr\Log\LoggerInterface;

/**
 * AmadeusRequestTransformer.php
 *
 * Build an Amadeus remarks request
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestTransformer
{
    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * @var null|string
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
        $wsdlPath = './wsdl/' . $this->config->itinerary->wsdl;

        $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
        if (!empty($this->config->itinerary->overrideHost)) {
            $soapClient->__setLocation($this->config->itinerary->overrideHost);
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
                    'receivedFrom' => 'service.itinerary',
                ],
            ]
        );

        if ($this->customSessionHandlerClass) {
            $clientParams->sessionHandler = new $this->customSessionHandlerClass($clientParams->sessionHandlerParams);
        }

        return $clientParams;
    }

    /**
     * build options for itinerary read (pnr_retrieve)
     *
     * @param string $recordLocator record locator to get pnr for
     *
     * @return Client\RequestOptions\PnrRetrieveOptions
     */
    public function buildOptionsItineraryRead($recordLocator)
    {
        return new Client\RequestOptions\PnrRetrieveOptions(
            [
                'recordLocator' => $recordLocator,
            ]
        );
    }
}
