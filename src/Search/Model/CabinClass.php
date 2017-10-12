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

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @param \stdClass $groupOfFares
     */
    public function __construct(\stdClass $groupOfFares)
    {
        $this->code = $groupOfFares->productInformation->cabinProduct->cabin ?? '';
        $this->name = self::$map[$this->code] ?? '';
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
}
