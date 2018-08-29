<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use Flight\Service\Amadeus\Search\Model\FreeBaggageIndex;

/**
 * FreeBaggageIndexTest.php
 *
 * Test the functionality of the class
 *
 * @covers Flight\Service\Amadeus\Search\Model\FreeBaggageIndex
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FreeBaggageIndexTest extends \Codeception\Test\Unit
{
    public function testBaggage()
    {
        $sendResult = new SendResult();
        $sendResult->responseObject = json_decode(json_encode(simplexml_load_file(
            codecept_data_dir('fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt-no-header.xml')
        )));

        $freeBaggageAllowanceGroups = [
            1 => $sendResult->responseObject->serviceFeesGrp->freeBagAllowanceGrp[0]->freeBagAllownceInfo->baggageDetails,
            2 => $sendResult->responseObject->serviceFeesGrp->freeBagAllowanceGrp[1]->freeBagAllownceInfo->baggageDetails,
            3 => $sendResult->responseObject->serviceFeesGrp->freeBagAllowanceGrp[2]->freeBagAllownceInfo->baggageDetails,
            4 => $sendResult->responseObject->serviceFeesGrp->freeBagAllowanceGrp[3]->freeBagAllownceInfo->baggageDetails,
        ];

        $index = new FreeBaggageIndex(new Result($sendResult));

        // paths which lead to group 1
        $this->assertEquals($freeBaggageAllowanceGroups[1], $index->getFreeBagAllowanceInfo(1, 1, 1));
        $this->assertEquals($freeBaggageAllowanceGroups[1], $index->getFreeBagAllowanceInfo(1, 2, 1));

        $this->assertEquals($freeBaggageAllowanceGroups[1], $index->getFreeBagAllowanceInfo(2, 1, 1));
        $this->assertEquals($freeBaggageAllowanceGroups[1], $index->getFreeBagAllowanceInfo(2, 1, 2));

        // paths which lead to group 3
        $this->assertEquals($freeBaggageAllowanceGroups[2], $index->getFreeBagAllowanceInfo(3, 1, 1));
        $this->assertEquals($freeBaggageAllowanceGroups[2], $index->getFreeBagAllowanceInfo(3, 2, 1));

        // paths which lead to group 4
        $this->assertEquals($freeBaggageAllowanceGroups[3], $index->getFreeBagAllowanceInfo(4, 1, 1));

        // paths which lead to group 5
        $this->assertEquals($freeBaggageAllowanceGroups[4], $index->getFreeBagAllowanceInfo(5, 1, 1));

        $this->assertNull($index->getFreeBagAllowanceInfo(0, 0, 0));
    }
}
