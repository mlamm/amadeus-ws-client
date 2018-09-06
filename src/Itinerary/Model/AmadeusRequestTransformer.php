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
        $wsdlPath = './wsdl/' . $this->config->itinerary->wsdl;
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
                'receivedFrom' => 'service.itinerary',
            ],
        ];

        // override soap client for tests
        if (!empty($this->config->itinerary->overrideHost)) {
            $soapClient = new \SoapClient($wsdlPath, ['trace' => 1]);
            $soapClient->__setLocation($this->config->itinerary->overrideHost);

            $params['sessionHandlerParams']['overrideSoapClient']         = $soapClient;
            // wsdl-hash that is internally used to match the right soap-client
            $params['sessionHandlerParams']['overrideSoapClientWsdlName'] = '16dbc24b';
        }

        return new Client\Params($params);
    }

    /**
     * build options for itinerary read (pnr_retrieve)
     *
     * @param string $recordLocator record locator to get pnr for
     *
     * @return Client\RequestOptions\PnrRetrieveOptions
     */
    public function buildOptionsItineraryRead($recordLocator = null)
    {
        $options = [];
        if ($recordLocator) {
            $options['recordLocator'] = $recordLocator;
        }
        return new Client\RequestOptions\PnrRetrieveOptions($options);
    }
}
