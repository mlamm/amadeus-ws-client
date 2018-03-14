<?php

namespace Helper\Unit;

use Codeception\Stub;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Model\Itinerary;
use Flight\Service\Amadeus\Remarks\Model\Remark;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;
use Helper\Unit;

/**
 * Class Remarks
 *
 * @author    Martin Leske <martin.leske@invia.de>
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 */
class Remarks extends Unit
{

    /**
     * generate response xml fixture files for remarks
     *
     * @param $jsonFixture string path to file
     *
     * @return \SimpleXMLElement
     */
    public function generateRemarksResponse($jsonFixture)
    {
        return simplexml_load_string(file_get_contents(codecept_data_dir($jsonFixture)));
    }

    /**
     * generate remarks add request
     *
     * @param $jsonFixture string path to file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateRemarksAddRequest($jsonFixture)
    {
        return file_get_contents(codecept_data_dir($jsonFixture));
    }

    /**
     * generate a ResultResponse
     * @return object
     * @throws \Exception
     */
    public function generateResultResponseWithContent()
    {
        $remark     = new Remark();
        $remark->setValue('6244')->setName('IBEBZIP');
        $readResult = Stub::make(Itinerary::class, ['getRemarks' => [$remark]]);
        return Stub::make(
            ResultResponse::class,
            [
                'getResult'  => new ArrayCollection([$readResult]),
            ]
        );
    }

    /**
     * generate the authHeader for Remarks from json file
     *
     * @param $jsonFixture string path to file
     *
     * @return array
     */
    public function generateAuthHeader($jsonFixture)
    {
        return file_get_contents(codecept_data_dir($jsonFixture));
    }
}