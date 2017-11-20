<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Carriers;
use Flight\Service\Amadeus\Search\Model\NodeList;
use Flight\Service\Amadeus\Search\Model\ValidatingCarrier;

/**
 * ValidatingCarrierTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\ValidatingCarrier
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ValidatingCarrierTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it extracts the carrier if available and writes it to the carriers structure
     *
     * @dataProvider provideTestCasesWithCarriers
     */
    public function testItSetsCarrier(string $fareProducts, string $expectedCarrier)
    {
        $fareProductsNode = new \SimpleXMLElement($fareProducts);

        $object = new ValidatingCarrier(new NodeList($fareProductsNode));
        $carriers = $object->addToCarriers(new Carriers());

        $this->assertEquals($expectedCarrier, $carriers->getValidating()->getIata());
    }

    public function provideTestCasesWithCarriers()
    {
        return [
            'single nodes' => [
                'fareProducts' => '
                    <paxFareProduct>
                        <paxFareDetail>
                            <paxFareNum>1</paxFareNum>
                            <totalFareAmount>87.49</totalFareAmount>
                            <totalTaxAmount>0.00</totalTaxAmount>
                            <codeShareDetails>
                                <transportStageQualifier>V</transportStageQualifier>
                                <company>4U</company>
                            </codeShareDetails>
                        </paxFareDetail>
                    </paxFareProduct>
                ',
                'expectedCarrier' => '4U',
            ],
            'multiple codeShareDetails nodes' => [
                'fareProducts' => '
                    <paxFareProduct>
                        <paxFareDetail>
                            <paxFareNum>1</paxFareNum>
                            <totalFareAmount>87.49</totalFareAmount>
                            <totalTaxAmount>0.00</totalTaxAmount>
                            <codeShareDetails>
                                <transportStageQualifier>V</transportStageQualifier>
                                <company>4U</company>
                            </codeShareDetails>
                            <codeShareDetails>
                                <transportStageQualifier>X</transportStageQualifier>
                                <company>AB</company>
                            </codeShareDetails>
                        </paxFareDetail>
                    </paxFareProduct>
                ',
                'expectedCarrier' => '4U',
            ],
            'multiple paxFareDetail nodes' => [
                'fareProducts' => '
                    <paxFareProduct>
                        <paxFareDetail>
                            <paxFareNum>1</paxFareNum>
                            <totalFareAmount>87.49</totalFareAmount>
                            <totalTaxAmount>0.00</totalTaxAmount>
                            <codeShareDetails>
                                <transportStageQualifier>V</transportStageQualifier>
                                <company>4U</company>
                            </codeShareDetails>
                            <codeShareDetails>
                                <transportStageQualifier>X</transportStageQualifier>
                                <company>AB</company>
                            </codeShareDetails>
                        </paxFareDetail>
                        <paxFareDetail>
                        </paxFareDetail>
                    </paxFareProduct>
                ',
                'expectedCarrier' => '4U',
            ],
        ];
    }

    /**
     * @dataProvider provideTestCasesMissingCarriers
     */
    public function testItIgnoresMissingCarrier(string $fareProducts)
    {
        $fareProductsNode = new \SimpleXMLElement($fareProducts);

        $object = new ValidatingCarrier(new NodeList($fareProductsNode));
        $carriers = $object->addToCarriers(new Carriers());

        $this->expectException('TypeError');
        $carriers->getValidating();
    }

    public function provideTestCasesMissingCarriers()
    {
        return [
            'no paxFareDetail node' => [
                'fareProducts' => '
                    <paxFareProduct>
                    </paxFareProduct>
                ',
            ],
            'no codeShareDetails nodes' => [
                'fareProducts' => '
                    <paxFareProduct>
                        <paxFareDetail>
                        </paxFareDetail>
                    </paxFareProduct>
                ',
                'expectedCarrier' => '4U',
            ],
        ];
    }

}
