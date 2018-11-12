<?php

namespace Flight\Service\Amadeus\Remarks\Service;

use Amadeus\Client\Session\Handler\WsdlAnalyser;
use Codeception\Test\Unit;
use Symfony\Component\Yaml\Yaml;

/**
 * This test is loading the wsdl specified in the config-file
 * and comparing the webservice version that the lib is going to use.
 *
 * Since multiple webservice endpoints can be in the wsdl,
 * we want to assert that our version of choice is used in all environments.
 * (lib uses the first entry in the wsdl for the webservice, not necessarily the newest version)
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class GetActiveVersionForPricePNRWithBookingClassTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Test version of used *Fare_PricePNRWithBookingClass* webservice.

     * @dataProvider configFileProvider
     * @param string $configFile config file path
     */
    public function testPriceVersion($configFile)
    {
        $config = Yaml::parse(
            file_get_contents($configFile),
            Yaml::PARSE_OBJECT_FOR_MAP
        );

        // should have at leats more chance than its prefix ("/admin/") and some spare chars
        self::assertGreaterThan(10, strlen($config->price->wsdl));
        $messagesAndVersions = WsdlAnalyser::loadMessagesAndVersions([
            'wsdl/' . $config->price->wsdl
        ]);

        self::assertTrue(isset($messagesAndVersions['Fare_PricePNRWithBookingClass']));
        $versionInfo = $messagesAndVersions['Fare_PricePNRWithBookingClass'];
        self::assertEquals('16.1', $versionInfo['version'], sprintf(
            "Version mismatch for given wsdl in config file '%s'",
            basename($configFile)
        ));
    }

    /**
     * Providing all the config files matching *.yml in config/ directory.
     *
     * @return array
     */
    public function configFileProvider(): array
    {
        $configFiles = glob(__DIR__ . '/../../../config/*.yml');
        $return = [];

        foreach ($configFiles as $configFile) {
            $return[] = [$configFile];
        }

        return $return;
    }
}
