<?php

namespace Flight\Service\Amadeus\Remarks\Service;

use Amadeus\Client\RequestOptions\FarePricePnrWithBookingClassOptions;
use Codeception\Test\Unit;
use Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder;

/**
 * Testing fare options builder with this test class.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class TarifOptionsBuilderTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Testing fare options getter using tarif *IATA*.
     *
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsIata()
    {
        // IATA = FXP/LO (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('IATA');
        $tarifOptions = $tarifOptionsBuilder->getTarifOptions();

        $this->assertCount(1, $tarifOptions);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[0]);
        $this->assertCount(1, $tarifOptions[0]->overrideOptions);
        $this->assertNotFalse(array_search('RLO', $tarifOptions[0]->overrideOptions));
    }

    /**
     * Testing fare options getter using tarif *NEGO*.
     *
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsNego()
    {
        // NEGO = FXP/LO/R,U (cryptic) + FXP/LO/R,UP (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('NEGO');
        $tarifOptions = $tarifOptionsBuilder->getTarifOptions();

        $this->assertCount(2, $tarifOptions);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[0]);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[1]);
        $this->assertCount(2, $tarifOptions[0]->overrideOptions);
        $this->assertCount(3, $tarifOptions[1]->overrideOptions);
        $this->assertNotFalse(array_search('RLO', $tarifOptions[0]->overrideOptions));
        $this->assertNotFalse(array_search('RU', $tarifOptions[0]->overrideOptions));
        $this->assertNotFalse(array_search('RLO', $tarifOptions[1]->overrideOptions));
        $this->assertNotFalse(array_search('RU', $tarifOptions[1]->overrideOptions));
        $this->assertNotFalse(array_search('RP', $tarifOptions[1]->overrideOptions));
    }

    /**
     * Testing fare options getter using tarif *NETALLU000867*.
     *
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsNetall()
    {
        // NETALLU000867 = FXP/LO/R,U000867 (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('NETALLU000867');
        $tarifOptions = $tarifOptionsBuilder->getTarifOptions();

        $this->assertCount(1, $tarifOptions);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[0]);
        $this->assertCount(2, $tarifOptions[0]->overrideOptions);
        $this->assertNotFalse(array_search('RLO', $tarifOptions[0]->overrideOptions));
        $this->assertNotFalse(array_search('RW', $tarifOptions[0]->overrideOptions));
        $this->assertSame('000867', $tarifOptions[0]->corporateUniFares[0]);
    }

    /**
     * Testing fare options getter using tarif *CALCPUB*.
     *
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsCalcPub()
    {
        // CALCPUB = FXP/LO/R,U000867 (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('CALCPUB');
        $tarifOptions = $tarifOptionsBuilder->getTarifOptions();

        $this->assertCount(1, $tarifOptions);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[0]);
        $this->assertCount(2, $tarifOptions[0]->overrideOptions);
        $this->assertNotFalse(array_search('RLO', $tarifOptions[0]->overrideOptions));
        $this->assertNotFalse(array_search('RW', $tarifOptions[0]->overrideOptions));
        $this->assertSame(['000867'], $tarifOptions[0]->corporateUniFares);
    }

    /**
     * Testing fare options getter using tarif *LOLOLOTRLOTLR*.
     *
     * @expectedException \RuntimeException
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsInvalidTarif()
    {
        // CALCPUB = FXP/LO/R,U000867 (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('LOLOLOTRLOTLR');
        $tarifOptionsBuilder->getTarifOptions();
    }

    /**
     * Test fare-options when a family-fare is given.
     *
     * @covers \Flight\Service\Amadeus\Price\Model\TarifOptionsBuilder::getTarifOptions
     */
    public function testGetTarifOptionsFamilyFare()
    {
        // CALCPUB = FXP/LO/R,U000867 (cryptic)
        $tarifOptionsBuilder = new TarifOptionsBuilder('CALCPUB', 'FLEX');
        $tarifOptions = $tarifOptionsBuilder->getTarifOptions();

        $this->assertCount(1, $tarifOptions);
        $this->assertInstanceOf(FarePricePnrWithBookingClassOptions::class, $tarifOptions[0]);
        $this->assertCount(2, $tarifOptions[0]->overrideOptions);
        $this->assertNotFalse(array_search('RLO', $tarifOptions[0]->overrideOptions));
        $this->assertNotFalse(array_search('RW', $tarifOptions[0]->overrideOptions));
        $this->assertSame(['000867'], $tarifOptions[0]->corporateUniFares);
        $this->assertSame('FLEX', $tarifOptions[0]->fareFamily);
    }
}
