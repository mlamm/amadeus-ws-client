<?php

namespace Flight\Service\Amadeus\Remarks\Service;

use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Flight\Service\Amadeus\Remarks\Model\RemarksAmadeusClient;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;

/**
 * ClientParamsFactoryTest.php
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class RemarksTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * before test happenings
     */
    protected function _before()
    {
//        $this->requestHelper = new RequestHelper();
        $this->config = json_decode(
            json_encode(
                [
                    'amadeus' => [
                        'sender'    => 'unittest',
                        'recipient' => 'amadeus unittest',
                        'endpoint'  => 'amadeus.unittest.com',
                    ],
                ]
            )
        );
    }

    /**
     * test for remarks read
     */
    public function testRemarksRead()
    {
        /** @var \Helper\Unit\Remarks $phpUnitHelper */
        $phpUnitHelper    = $this->getModule('\Helper\Unit\Remarks');
        $remarksClient    = Stub::make(RemarksAmadeusClient::class, ['remarksRead' => new ResultResponse()]);
        $remarksValidator = Stub::make(\Flight\Service\Amadeus\Remarks\Request\Validator\Remarks::class, [$this->config]);
        $serializer       = Stub::make(\JMS\Serializer\Serializer::class, ['serialize' => '']);

        $remarksService = new Remarks($remarksValidator, $serializer, $remarksClient, $this->config);

        $response = $remarksService->remarksRead(
            $phpUnitHelper->generateAuthHeader('fixtures/06-remarks_auth_header.json'),
            'AMWP'
        );


        $this->assertInternalType('string', $response);
    }

    /**
     * test for remarks add
     */
    public function testRemarksAdd()
    {
        /** @var \Helper\Unit\Remarks $phpUnitHelper */
        $phpUnitHelper    = $this->getModule('\Helper\Unit\Remarks');
        $remarksClient    = Stub::make(RemarksAmadeusClient::class, ['remarksAdd' => new ResultResponse()]);
        $remarksValidator = Stub::make(\Flight\Service\Amadeus\Remarks\Request\Validator\Remarks::class, [$this->config]);
        $serializer       = Stub::make(\JMS\Serializer\Serializer::class, ['serialize' => '']);
        $remarksService   = new Remarks($remarksValidator, $serializer, $remarksClient, $this->config);

        $response = $remarksService->remarksAdd(
            $phpUnitHelper->generateAuthHeader('fixtures/06-remarks_auth_header.json'),
            'UNIT',
            $phpUnitHelper->generateRemarksAddRequest('fixtures/07-add_remarks_body.json')
        );

        $this->assertInternalType('string', $response);
    }

    /**
     * test for remarks modify
     */
    public function testRemarksModify()
    {
        /** @var \Helper\Unit\Remarks $phpUnitHelper */
        $phpUnitHelper    = $this->getModule('\Helper\Unit\Remarks');
        $resultResponse   = $phpUnitHelper->generateResultResponseWithContent();
        $remarksClient    = Stub::make(
            RemarksAmadeusClient::class,
            [
                'remarksRead'   => $resultResponse,
                'remarksAdd'    => new ResultResponse(),
                'remarksDelete' => new ResultResponse(),
            ]
        );
        $remarksValidator = Stub::make(\Flight\Service\Amadeus\Remarks\Request\Validator\Remarks::class, [$this->config]);
        $serializer       = Stub::make(\JMS\Serializer\Serializer::class, ['serialize' => '']);
        $remarksService   = new Remarks($remarksValidator, $serializer, $remarksClient, $this->config);

        $response = $remarksService->remarksModify(
            $phpUnitHelper->generateAuthHeader('fixtures/06-remarks_auth_header.json'),
            'UNIT',
            $phpUnitHelper->generateRemarksAddRequest('fixtures/08-modify_remarks_body.json')
        );

        $this->assertInternalType('string', $response);
    }

    /**
     * test for remarks delete
     */
    public function testRemarksDelete()
    {
        /** @var \Helper\Unit\Remarks $phpUnitHelper */
        $phpUnitHelper    = $this->getModule('\Helper\Unit\Remarks');
        $resultResponse   = $phpUnitHelper->generateResultResponseWithContent();
        $remarksClient    = Stub::make(RemarksAmadeusClient::class, ['remarksRead' => $resultResponse, 'remarksDelete' => new ResultResponse()]);
        $remarksValidator = Stub::make(\Flight\Service\Amadeus\Remarks\Request\Validator\Remarks::class, [$this->config]);
        $serializer       = Stub::make(\JMS\Serializer\Serializer::class, ['serialize' => '']);
        $remarksService   = new Remarks($remarksValidator, $serializer, $remarksClient, $this->config);

        $response = $remarksService->remarksDelete(
            $phpUnitHelper->generateAuthHeader('fixtures/06-remarks_auth_header.json'),
            'UNIT',
            $phpUnitHelper->generateRemarksAddRequest('fixtures/07-add_remarks_body.json')
        );

        $this->assertInternalType('string', $response);
    }
}
