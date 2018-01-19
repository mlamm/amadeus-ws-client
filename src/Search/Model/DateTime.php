<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

/**
 * DateTime.php
 *
 * Provide a uniform way to parse AMA dates
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class DateTime
{
    /**
     * Convert the date-time string to an object
     *
     * @param string $dateTime
     * @return \DateTime
     */
    public static function fromDateTime(string $dateTime): \DateTime
    {
        return \DateTime::createFromFormat('dmyHi', $dateTime);
    }

    /**
     * Convert the date and time strings to an object
     *
     * @param string $date
     * @param string $time
     * @return \DateTime
     */
    public static function fromDateAndTime(string $date, string $time): \DateTime
    {
        return self::fromDateTime("{$date}{$time}");
    }
}
