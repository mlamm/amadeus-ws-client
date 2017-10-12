<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use Amadeus\Client\Result;
use Amadeus\Client\Session\Handler\SendResult;
use AmadeusService\Search\Model\FreeBaggageIndex;

/**
 * FreeBaggageIndexTest.php
 *
 * Test the functionality of the class
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FreeBaggageIndexTest extends \Codeception\Test\Unit
{
    public function testBaggage()
    {
        $this->markTestSkipped('free baggage not completed yet');

        $sendResult = new SendResult();
        $sendResult->responseObject = json_decode(json_encode(simplexml_load_file(
            codecept_data_dir('fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt.xml')
        )));

        $index = new FreeBaggageIndex(new Result($sendResult));
    }
}
