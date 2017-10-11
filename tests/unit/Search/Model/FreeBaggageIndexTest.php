<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Search\Model;

use AmadeusService\Search\Model\FreeBaggageIndex;


/**
 * FreeBaggageIndexTest.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class FreeBaggageIndexTest extends \Codeception\Test\Unit
{
    public function testFoo()
    {
        $amaResponse = json_decode(json_encode(simplexml_load_file(
            codecept_data_dir('fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt.xml')
        )));

        $index = new FreeBaggageIndex($amaResponse);

        $this->fail('not completed');
    }
}
