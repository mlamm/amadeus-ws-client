<?php

namespace Flight\Service\Amadeus\Price\Model;

use Amadeus\Client\RequestOptions\Fare\PricePnr\FareBasis;
use Amadeus\Client\RequestOptions\Fare\PricePnr\PaxSegRef;
use Amadeus\Client\RequestOptions\FarePricePnrWithBookingClassOptions;

/**
 * Builder class to build and return fare options for a given tariff.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class TarifOptionsBuilder
{
    public const COORP_CODE = '000867';

    public const COORP_CODE_NETALLU513058 = '513058';

    public const COORP_CODE_NETALLU176212 = '176212';

    public const COORP_CODE_NETALLU374186 = '374186';

    public const COORP_CODE_NETALLU020481 = '020481';

    /**
     * Tarif to build options for.
     *
     * @var string
     */
    private $tarif;

    /**
     * TarifOptionsBuilder constructor.
     *
     * @param string $tarif tarif to build options for
     */
    public function __construct($tarif)
    {
        $this->tarif = $tarif;
    }

    /**
     * Build fare options for given tarif, there can be multiple.
     *
     * @return FarePricePnrWithBookingClassOptions[]
     */
    public function getTarifOptions(): array
    {
        if (empty($this->tarif)) {
            throw new \InvalidArgumentException('Tarif is not set!');
        }

        $options = [];

        if ($this->tarif === 'IATA') {
            $bleh = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                        'PFF',
                    ],
                    'pricingsFareBasis' => [
                        new FareBasis([
                            'fareBasisCode' => 'FF',
                        ])
                    ]
                ]
            );
            $bleh->optionDetail = 'bleeh';
//            $options[] = new FarePricePnrWithBookingClassOptions([
//                    'overrideOptions' => [
//                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
//                        'PFF',
//                    ],
//                ]
//            );
            $options[] = $bleh;
        } elseif ($this->tarif === 'NEGO') {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_UNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                ]
            );
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_UNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_PUB,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                ]
            );
        } elseif (\in_array($this->tarif, ['NETALLU000867', 'CALCPUB'], true)) {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE]
                ]
            );
        } elseif ($this->tarif === 'NETALLU513058') {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE_NETALLU513058]
                ]
            );
        } elseif ($this->tarif === 'NETALLU176212') {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE_NETALLU176212]
                ]
            );
        } elseif ($this->tarif === 'NETALLU374186') {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE_NETALLU374186]
                ]
            );
        } elseif ($this->tarif === 'NETALLU020481') {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE_NETALLU020481]
                ]
            );
        } else {
            throw new \RuntimeException(sprintf("Invalid tarif '%s' provided!", $this->tarif));
        }

        if (\count($options) > 1) {
            throw new \RuntimeException(
                'Options-Validation failed, any more than 1 options will lead to a separate pricing-request!'
            );
        }

        return $options;
    }
}
