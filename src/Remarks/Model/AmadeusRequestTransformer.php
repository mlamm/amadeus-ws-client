<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

use Amadeus\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Request\Entity\Authenticate;
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
     * @param \stdClass $config
     * @param null|string     $customSessionHandlerClass
     */
    public function __construct(\stdClass $config, $customSessionHandlerClass = null)
    {
        $this->config = $config;
        $this->customSessionHandlerClass = $customSessionHandlerClass;
    }

    /**
     * builds the client
     *
     * @param Authenticate $authentication
     * @param LoggerInterface $logger
     *
     * @return Client\Params
     */
    public function buildClientParams(Authenticate $authentication, LoggerInterface $logger) : Client\Params
    {
        $clientParams = new Client\Params(
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
                    'stateful' => false,
                    'wsdl' => "./wsdl/{$this->config->remarks->wsdl}",
                    'logger' => $logger
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'service.remarks'
                ]
            ]
        );

        if ($this->customSessionHandlerClass) {
            $clientParams->sessionHandler = new $this->customSessionHandlerClass($clientParams->sessionHandlerParams);
        }

        return $clientParams;
    }

    public function buildOptionsRemarksRead($recordlocator)
    {
        return new Client\RequestOptions\PnrRetrieveOptions(['recordLocator' => $recordlocator]);
    }

    public function buildOptionsRemarksAdd($recordlocator, ArrayCollection $remarks)
    {
        $elements = [];
        /** @var Remark $remark */
        foreach ($remarks as $remark) {
            $elements[] = (new \Amadeus\Client\RequestOptions\Pnr\Element\MiscellaneousRemark([
                'type'     => $remark->getType() ? $remark->getType() : 'RM',
                'text'     => $remark->convertToCrs(),
                //'category' => '*' /** 1-character Category indicator */ will be put in front of the name
            ]));
        }

        return new Client\RequestOptions\PnrAddMultiElementsOptions([
            'recordLocator' => $recordlocator,
            'actionCode'    => Client\RequestOptions\PnrCancelOptions::ACTION_END_TRANSACT_RETRIEVE,
            'elements'      => $elements
        ]);
    }

    public function buildOptionsRemarksDelete($recordlocator, ArrayCollection $remarks)
    {
        $elements = [];
        /** @var Remark $remark */

        foreach ($remarks as $remark) {
            $elements[] = $remark->getManagementData()->getReference()->getNumber();
        }

        return new Client\RequestOptions\PnrCancelOptions([
            'recordLocator'    => $recordlocator,
            'actionCode'       => Client\RequestOptions\PnrCancelOptions::ACTION_END_TRANSACT_RETRIEVE,
            'elementsByTattoo' => $elements
        ]);
    }
}
