<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Helper;

/**
 * RequestFaker.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class RequestFaker
{
    /**
     * returns relative fixed departure timestamp
     * from today first day of month after next month 8:00:00
     *
     * @return int
     */
    public static function getDepartureTimestamp() : int
    {
        $ts = strtotime('first day of next month', strtotime('first day of next month'));
        $date = new \DateTime('@' . $ts);
        $date->setTime(8,0,0);

        return $date->getTimestamp();
    }

    /**
     * returns timestamp for date 7 days after departure (first day of month after next month 8:00:00)
     *
     * @return int
     */
    public static function getReturnDepartureTimestamp() : int
    {
        $departureTs = self::getDepartureTimestamp();

        $date = new \DateTime('@'.$departureTs);
        $date->add(new \DateInterval('P7D'));

        return $date->getTimestamp();
    }
}
