<?php
declare(strict_types = 1);

namespace Flight\Service\Amadeus\Itinerary\Model;

use Amadeus\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Request\Entity\Authenticate;
use Flight\Service\Amadeus\Itinerary\Request\Entity\Session;
use Psr\Log\LoggerInterface;

/**
 * AmadeusRequestTransformer.php
 *
 * Build an Amadeus remarks request
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class AmadeusRequestTransformer
{
    /**
     * @var \stdClass
     */
    protected $config;

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
                    'wsdl'              => "./wsdl/{$this->config->itinerary->wsdl}",
                    'logger'            => $logger,
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
     * @param                 $recordLocator
     *
     * @return Client\RequestOptions\PnrRetrieveOptions
     */
    public function buildOptionsItineraryRead($recordLocator)
    {
        return new Client\RequestOptions\PnrRetrieveOptions(
            [
                'recordLocator' => $recordLocator
            ]
        );
    }
}
