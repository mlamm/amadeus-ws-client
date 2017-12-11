<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Service\Amadeus\Search\Model\DateTime;

/**
 * DateTimeTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\DateTime
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class DateTimeTest extends \Codeception\Test\Unit
{
    /**
     * Does it convert if the date and time are combined in a single string?
     */
    public function testItConvertsDateTime()
    {
        $dateTime = DateTime::fromDateTime('1204130530');
        $this->assertEquals('2013-04-12 05:30', $dateTime->format('Y-m-d H:i'));
    }

    /**
     * Does it parse the date given as separate date and time strings?
     */
    public function testItConvertsSeparateDateAndTime()
    {
        $dateTime = DateTime::fromDateAndTime('120413', '0530');
        $this->assertEquals('2013-04-12 05:30', $dateTime->format('Y-m-d H:i'));
    }
}
