<?php
namespace Search;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ConversionRate\ConversionRateDetail;
use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ReplyStatus\Status;
use AmadeusService\Search\Model\AmadeusResponseParser;


class ResponseParserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testParseResponse()
    {
        $path = __DIR__ . '/../../_support/fixtures/dummy-response-new.xml';

        $responseParser = new AmadeusResponseParser();
        $response = $responseParser->parse(file_get_contents($path));

        die(print_r($response, true));

        // reply-status
        $this->tester->assertInstanceOf(
            Status::class,
            $response->getReplyStatus()->first()
        );
        $this->tester->assertEquals(
            'FQX',
            $response->getReplyStatus()->first()->getAdvisoryTypeInfo()
        );

        // conversion rate
        $this->tester->assertCount(2, $response->getConversionRate());
        $this->tester->assertInstanceOf(
            ConversionRateDetail::class,
            $response->getConversionRate()->first()
        );
        $this->tester->assertEquals(
            'EUR',
            $response->getConversionRate()->first()->getCurrency()
        );
        $this->tester->assertEquals(
            1,
            $response->getConversionRate()->offsetGet(1)->getConvertedAmountLink()
        );


    }
}