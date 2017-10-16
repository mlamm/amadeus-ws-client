<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

/**
 * CabinClass.php
 *
 * Convert the cabin class info
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CabinClass
{
    private static $map = [
        'C' => 'Business Class', // Business
        'F' => 'First Class', // First, supersonic
        'M' => 'Economy', // Economic Standard
        'W' => 'Economy', // Economic Premium
        'Y' => 'Economy', // Economic
    ];

    public static function name(\stdClass $groupOfFares)
    {
        return self::$map[self::code($groupOfFares)] ?? '';
    }

    public static function code(\stdClass $groupOfFares)
    {
        return $groupOfFares->productInformation->cabinProduct->cabin ?? '';
    }
}
