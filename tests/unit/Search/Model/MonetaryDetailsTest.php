<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Service\Amadeus\Search\Model\MonetaryDetails;

/**
 * MonetaryDetailsTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\MonetaryDetails
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class MonetaryDetailsTest extends \Codeception\Test\Unit
{
    /**
     * Does the constructor correctly import values from the given list of objects?
     */
    public function testItExtractsTicketingFees()
    {
        $object = new MonetaryDetails([
            (object) [
                'amount' => '123.00',
            ],
            (object) [
                'amountType' => 'XOB',
                'amount' => '123.00',
            ],
            (object) [
                'amountType' => 'OB',
                'amount' => '52.00',
            ],
        ]);

        $this->assertTrue($object->hasTicketingFeesTotal());
        $this->assertSame(52.00, $object->getTicketingFeesTotal());
        $this->assertTrue($object->hasTotalWithoutTicketingFees());
        $this->assertSame(123.00, $object->getTotalWithoutTicketingFees());
    }

    /**
     * Does the object give reasonable results if the input is empty?
     */
    public function testItAcceptsEmptyList()
    {
        $object = new MonetaryDetails([]);
        $this->assertFalse($object->hasTicketingFeesTotal());
        $this->assertNull($object->getTicketingFeesTotal());
        $this->assertFalse($object->hasTotalWithoutTicketingFees());
        $this->assertNull($object->getTotalWithoutTicketingFees());
    }

    /**
     * Does the named constructor build the object from a <recommendation> node?
     */
    public function testItBuildsFromRecommendation()
    {
        $recommendationNode = new \SimpleXMLElement('
            <recommendation>
                <recPriceInfo>
                    <monetaryDetail>
                        <amount>889.42</amount>
                    </monetaryDetail>
                    <monetaryDetail>
                        <amount>498.42</amount>
                    </monetaryDetail>
                    <monetaryDetail>
                        <amountType>OB</amountType>
                        <amount>57.00</amount>
                    </monetaryDetail>
                    <monetaryDetail>
                        <amountType>XOB</amountType>
                        <amount>832.42</amount>
                    </monetaryDetail>
                    <monetaryDetail>
                        <amountType>F</amountType>
                        <amount>336.00</amount>
                    </monetaryDetail>
                    <monetaryDetail>
                        <amountType>Q</amountType>
                        <amount>0.00</amount>
                    </monetaryDetail>
                </recPriceInfo>
            </recommendation>
        ');

        $object = MonetaryDetails::fromRecommendation(json_decode(json_encode($recommendationNode)));

        $this->assertTrue($object->hasTicketingFeesTotal());
        $this->assertSame(57.0, $object->getTicketingFeesTotal());
        $this->assertTrue($object->hasTotalWithoutTicketingFees());
        $this->assertSame(832.42, $object->getTotalWithoutTicketingFees());
    }

    /**
     * Does the named constructor build the object from an empty <recommendation> node?
     */
    public function testItBuildsFromEmptyRecommendation()
    {
        $recommendationNode = new \SimpleXMLElement('<recommendation/>');

        $object = MonetaryDetails::fromRecommendation(json_decode(json_encode($recommendationNode)));

        $this->assertFalse($object->hasTicketingFeesTotal());
        $this->assertFalse($object->hasTotalWithoutTicketingFees());
    }
}
