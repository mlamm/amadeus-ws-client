<?php

namespace Flight\Service\Amadeus\Price\Model;

use Amadeus\Client\RequestOptions\FarePricePnrWithBookingClassOptions;

/**
 * Builder class to build and return fare options for a given tarif.
 *
 * @author     Marcel Lamm <marcel.lamm@invia.de>
 * @copyright  Copyright (c) 2018 Invia Flights Germany GmbH
 */
class TarifOptionsBuilder
{
    const COORP_CODE = '000867';

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
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST],
                ]
            );
        } else if ($this->tarif === 'NEGO') {
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
        } else if (\in_array($this->tarif, ['NETALLU000867', 'CALCPUB'], true)) {
            $options[] = new FarePricePnrWithBookingClassOptions([
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_CORPUNI,
                        FarePricePnrWithBookingClassOptions::OVERRIDE_RETURN_LOWEST,
                    ],
                    'corporateUniFares' => [self::COORP_CODE]
                ]
            );
        } else {
            throw new \RuntimeException(sprintf("Invalid tarif '%s' provided!", $this->tarif));
        }

        return $options;
    }
}
