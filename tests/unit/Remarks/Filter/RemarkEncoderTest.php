<?php

namespace unit\Remarks\Filter;

use Flight\Service\Amadeus\Remarks\Filter\RemarkEncoder;
use Codeception\Test\Unit;

/**
 * RemarkEncoderTest.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class RemarkEncoderTest extends Unit
{
    /**
     * Test various remark-filters for their correct filtering of special chars.
     *
     * @dataProvider remarkProvider
     * @covers       EncodingFilterCommon
     * @covers       EncodingFilterMail
     * @covers       EncodingFilterName
     *
     * @param string $inputName      input name of remark
     * @param string $inputValue     input value of remark
     * @param string $expectedResult expected resulting filtered remark (key+name)
     */
    public function testRemarkEncoder($inputName, $inputValue, $expectedResult) : void
    {
        $encoder = new RemarkEncoder($inputName, $inputValue);
        \PHPUnit_Framework_Assert::assertSame($expectedResult, $encoder->get());
    }

    /**
     * @return array
     */
    public function remarkProvider() : array
    {
        return [
            ['IBEBSTREET', 'Hallo_Flüge-Team', 'HALLO FLUEGE TEAM'],
            ['IBEBSTREET', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBSTREET', 'Hallo,# Flüge&/()\ Team!', 'HALLO FLUEGE TEAM'],

            ['IBEBCOMPANY', 'Hallo_Flüge-Team', 'HALLO FLUEGE TEAM'],
            ['IBEBCOMPANY', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBCOMPANY', 'Hallo,# Flüge&/()\ Team!', 'HALLO FLUEGE TEAM'],

            ['IBEBZIP', 'Hallo_Flüge-Team', 'HALLO FLUEGE TEAM'],
            ['IBEBZIP', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBZIP', 'Hallo,# Flüge&/()\ Team!', 'HALLO FLUEGE TEAM'],

            ['IBEBCITY', 'Hallo_Flüge-Team', 'HALLO FLUEGE TEAM'],
            ['IBEBCITY', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBCITY', 'Hallo,# Flüge&/()\ Team!', 'HALLO FLUEGE TEAM'],

            ['IBEBCOUNTRY', 'Hallo_Flüge-Team', 'HALLO FLUEGE TEAM'],
            ['IBEBCOUNTRY', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBCOUNTRY', 'Hallo,# Flüge&/()\ Team!', 'HALLO FLUEGE TEAM'],

            ['IBEBNAME', 'Hallo_Flüge-Team', 'HALLO_FLUEGE-TEAM'],
            ['IBEBNAME', 'Hallo Flüge 323 Team', 'HALLO FLUEGE 323 TEAM'],
            ['IBEBNAME', 'Hallo,# Flüge&/()\ Team!', 'HALLO,# FLUEGE&/()\ TEAM!'],

            ['IBEBEMAIL', 'marcel.lamm@invia.de', 'MARCEL.LAMM-AT-INVIA.DE'],
            ['IBEBEMAIL', 'marcel_lamm@invia.de', 'MARCEL-CHR095-LAMM-AT-INVIA.DE'],
            ['IBEBEMAIL', 'sänßdrö_at_keIl@unisTer.de', 'SAENSSDROE-CHR095-AT-CHR095-KEIL-AT-UNISTER.DE'],
            ['IBEBEMAIL', 'sändrö-at-keIl@unister.de', 'SAENDROE-AT-KEIL-AT-UNISTER.DE'],

            ['IBEPAXEMAIL', 'marcel.lamm@invia.de', 'MARCEL.LAMM-AT-INVIA.DE'],
            ['IBEPAXEMAIL', 'marcel_lamm@invia.de', 'MARCEL-CHR095-LAMM-AT-INVIA.DE'],
            ['IBEPAXEMAIL', 'sänßdrö_at_keIl@unisTer.de', 'SAENSSDROE-CHR095-AT-CHR095-KEIL-AT-UNISTER.DE'],
            ['IBEPAXEMAIL', 'sändrö-at-keIl@unister.de', 'SAENDROE-AT-KEIL-AT-UNISTER.DE'],
        ];
    }
}
