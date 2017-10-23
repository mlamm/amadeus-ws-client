<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

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
    /**
     * @var string[]
     */
    private static $map = [
        'C' => 'Business Class', // Business
        'F' => 'First Class', // First, supersonic
        'M' => 'Economy', // Economic Standard
        'W' => 'Economy', // Economic Premium
        'Y' => 'Economy', // Economic
    ];

    /**
     * Extract name of cabin class from groupOfFares node
     *
     * @param \stdClass $groupOfFares
     * @return array|mixed|string
     */
    public static function name(\stdClass $groupOfFares) : string
    {
        return self::$map[self::code($groupOfFares)] ?? '';
    }

    /**
     * @param \stdClass $groupOfFares
     * @return string
     */
    public static function code(\stdClass $groupOfFares) : string
    {
        return $groupOfFares->productInformation->cabinProduct->cabin ?? '';
    }

    /**
     * @param \stdClass $groupOfFares
     * @return string
     */
    public static function rbd(\stdClass $groupOfFares) : string
    {
        return $groupOfFares->productInformation->cabinProduct->rbd ?? '';
    }
}
