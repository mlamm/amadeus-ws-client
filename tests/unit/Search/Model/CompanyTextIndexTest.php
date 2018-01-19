<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use Flight\Service\Amadeus\Search\Model\CompanyTextIndex;

/**
 * CompanyTextIndexTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\CompanyTextIndex
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CompanyTextIndexTest extends \Codeception\Test\Unit
{
    /**
     * Does it build the index correctly for multiple entries?
     */
    public function testItBuildsIndexFromResult()
    {
        $result = new SendResult();
        $result->responseObject = json_decode(json_encode(new \SimpleXMLElement('
            <Fare_MasterPricerTravelBoardSearchReply xmlns="http://xml.amadeus.com/FMPTBR_10_2_1A">
                <replyStatus>
                    <status>
                        <advisoryTypeInfo>FQX</advisoryTypeInfo>
                    </status>
                </replyStatus>
                <companyIdText>
                    <textRefNumber>79</textRefNumber>
                    <companyText>LUFTHANSA OR LH CITYLINE</companyText>
                </companyIdText>
                <companyIdText>
                    <textRefNumber>77</textRefNumber>
                    <companyText>AIR CANADA EXPRESS - JAZZ</companyText>
                </companyIdText>
                <flightIndex/>
                <recommendation/>
            </Fare_MasterPricerTravelBoardSearchReply>
        ')));

        $index = CompanyTextIndex::fromSearchResult(new Result($result));

        $this->assertArrayHasKey('79', $index);
        $this->assertEquals('LUFTHANSA OR LH CITYLINE', $index['79']);
        $this->assertArrayHasKey('77', $index);
        $this->assertEquals('AIR CANADA EXPRESS - JAZZ', $index['77']);

        $this->assertArrayNotHasKey('80', $index);
    }

    /**
     * Does it build the index correctly for a single entry?
     */
    public function testItBuildIndexFromSingleEntry()
    {
        $result = new SendResult();
        $result->responseObject = json_decode(json_encode(new \SimpleXMLElement('
            <Fare_MasterPricerTravelBoardSearchReply xmlns="http://xml.amadeus.com/FMPTBR_10_2_1A">
                <replyStatus>
                    <status>
                        <advisoryTypeInfo>FQX</advisoryTypeInfo>
                    </status>
                </replyStatus>
                <companyIdText>
                    <textRefNumber>77</textRefNumber>
                    <companyText>AIR CANADA EXPRESS - JAZZ</companyText>
                </companyIdText>
                <flightIndex/>
            <recommendation/>
            </Fare_MasterPricerTravelBoardSearchReply>
        ')));

        $index = CompanyTextIndex::fromSearchResult(new Result($result));

        $this->assertArrayHasKey('77', $index);
        $this->assertEquals('AIR CANADA EXPRESS - JAZZ', $index['77']);

        $this->assertArrayNotHasKey('80', $index);
    }
}
