<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use Flight\Service\Amadeus\Search\Model\LegIndex;

/**
 * LegIndexTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\LegIndex
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class LegIndexTest extends \Codeception\Test\Unit
{
    /**
     * Verify that access via the given references returns the correct nodes
     */
    public function testItIndexes()
    {
        $sendResult = new SendResult();
        $sendResult->responseObject = json_decode(json_encode(simplexml_load_file(
            codecept_data_dir('fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt-no-header.xml')
        )));

        $index = new LegIndex(new Result($sendResult));

        $groupOfFlights = $index->groupOfFlights('0', '1');
        $this->assertEquals('1', $groupOfFlights->propFlightGrDetail->flightProposal[0]->ref);

        $groupOfFlights = $index->groupOfFlights('0', '5');
        $this->assertEquals('5', $groupOfFlights->propFlightGrDetail->flightProposal[0]->ref);
    }
}
