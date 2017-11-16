<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Service\Amadeus\Search\Model\CabinClass;

/**
 * CabinClassTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\CabinClass
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CabinClassTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it creates the correct names for the cabin classes
     *
     * @dataProvider provideTestCodes
     */
    public function testItDeterminesName(string $amaCabin, string $expectedCode, string $expectedName)
    {
        $groupOfFares = json_decode(json_encode(new \SimpleXMLElement('
            <groupOfFares>
                <productInformation>
                    <cabinProduct>
                        <rbd>L</rbd>
                        <cabin>M</cabin>
                        <avlStatus>9</avlStatus>
                    </cabinProduct>
                    <fareProductDetail>
                        <fareBasis>L</fareBasis>
                        <passengerType>ADT</passengerType>
                        <fareType>RP</fareType>
                    </fareProductDetail>
                    <breakPoint>Y</breakPoint>
                </productInformation>
            </groupOfFares>
        ')));

        $groupOfFares->productInformation->cabinProduct->cabin = $amaCabin;

        $this->assertEquals($expectedCode, CabinClass::code($groupOfFares));
        $this->assertEquals($expectedCode, CabinClass::code($groupOfFares));
        $this->assertEquals('L', CabinClass::rbd($groupOfFares));
    }

    /**
     * @return array
     */
    public function provideTestCodes()
    {
        return [
            [
                'cabin' => 'C',
                'expectedCode' => 'C',
                'expectedName' => 'Business',
            ],
            [
                'cabin' => 'F',
                'expectedCode' => 'F',
                'expectedName' => 'First',
            ],
            [
                'cabin' => 'M',
                'expectedCode' => 'M',
                'expectedName' => 'Economy',
            ],
            [
                'cabin' => 'W',
                'expectedCode' => 'W',
                'expectedName' => 'Economy',
            ],
            [
                'cabin' => 'Y',
                'expectedCode' => 'Y',
                'expectedName' => 'Economy',
            ],
        ];
    }

    /**
     * Verify that it returns empty values on missing input (does not crash)
     */
    public function testItReturnsEmptyValuesOnInvalidInput()
    {
        $this->assertEquals('', CabinClass::name(new \stdClass));
        $this->assertEquals('', CabinClass::code(new \stdClass));
        $this->assertEquals('', CabinClass::rbd(new \stdClass));
    }
}
