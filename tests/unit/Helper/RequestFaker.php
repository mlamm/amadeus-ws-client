<?php
declare(strict_types=1);

namespace AmadeusService\Tests\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\BusinessCaseOptions;
use Flight\SearchRequestMapping\Entity\Leg;
use Flight\SearchRequestMapping\Entity\Request;

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
    const OPT_TYPE = 'type';
    const OPT_FLEXIBLE_LEG_DATES = 'flexibleDate';
    const OPT_AIRLINE_FILTER = 'airlineFilter';
    const OPT_CABIN_CLASS_FILTER = 'cabinClassFilter';
    const OPT_AREA_SEARCH = 'areaSearch';
    const OPT_PAX = 'pax';
    const OPT_RESULT_LIMIT = 'resultLimit';
    const OPT_IS_OVERNIGHT = 'isOvernight';

    /**
     * builds a whole Request with options given
     *
     * @param array $options
     *
     * @return Request
     */
    public static function getFakeRequest(array $options) : Request
    {
        $request = new Request;

        self::setPax($request, $options);

        $request->setLegs(self::getLegs($options));
        if (isset($options[self::OPT_AIRLINE_FILTER])) {
            $request->setFilterAirline($options[self::OPT_AIRLINE_FILTER]);
        }
        if (isset($options[self::OPT_CABIN_CLASS_FILTER])) {
            $request->setFilterCabinClass($options[self::OPT_CABIN_CLASS_FILTER]);
        }
        $request->setBusinessCases(new ArrayCollection());
        $request->getBusinessCases()->add(new ArrayCollection());
        $request->getBusinessCases()->first()->add(self::buildBusinessCase($options));

        return $request;
    }

    /**
     * Builds a one-way request with two adult passengers. All options can be overridden.
     *
     * @param array $options
     * @return Request
     */
    public static function buildDefaultRequest(array $options = []) : Request
    {
        $defaultOptions = [
            self::OPT_TYPE => 'one-way',
            self::OPT_PAX => [2],
        ];

        return self::getFakeRequest($options + $defaultOptions);
    }

    /**
     * returns relative fixed departure DateTime object
     * from today first day of month after next month 8:00:00
     *
     * @return \DateTime
     */
    public static function getDepartureDateTime() : \DateTime
    {
        $ts = strtotime('first day of next month', strtotime('first day of next month'));
        $date = new \DateTime('@' . $ts);
        $date->setTime(8,0,0);

        return $date;
    }

    /**
     * returns relative fixed departure DateTime
     * from today first day of month after next month 8:00:00
     *
     * @return int
     */
    public static function getDepartureTimestamp() : int
    {
        return (self::getDepartureDateTime())->getTimestamp();
    }

    /**
     * returns departure date as timestamp
     *
     * @return \DateTime
     */
    public static function getReturnDepartureDateTime() : \DateTime
    {
        $departureTs = self::getDepartureTimestamp();

        $date = new \DateTime('@'.$departureTs);
        $date->add(new \DateInterval('P7D'));

        return $date;
    }

    /**
     * returns timestamp for date 7 days after departure (8th day of month after next month 8:00:00)
     *
     * @return int
     */
    public static function getReturnDepartureTimestamp() : int
    {
        return (self::getReturnDepartureDateTime())->getTimestamp();
    }

    /**
     * builds legs part out of given options
     *
     * @param array $options
     *
     * @return ArrayCollection
     */
    public static function getLegs(array $options) : ArrayCollection
    {
        $legs = new ArrayCollection();

        $flexibleDateStart = false;
        $flexibleDateReturn = false;
        if (isset($options[self::OPT_FLEXIBLE_LEG_DATES]) && !empty($options[self::OPT_FLEXIBLE_LEG_DATES])) {
            if (isset($options[self::OPT_FLEXIBLE_LEG_DATES][0]) && true == (bool) $options[self::OPT_FLEXIBLE_LEG_DATES][0]) {
                $flexibleDateStart = true;
            }
            if (isset($options[self::OPT_FLEXIBLE_LEG_DATES][1]) && true == (bool) $options[self::OPT_FLEXIBLE_LEG_DATES][1]) {
                $flexibleDateReturn = true;
            }
        }
        $legs->add(self::buildLeg($flexibleDateStart, self::getDepartureDateTime()));

        if ($options[self::OPT_TYPE] == 'round-trip') {
            $legs->add(self::buildLeg($flexibleDateReturn, self::getReturnDepartureDateTime(), true));
        }

        return $legs;
    }

    /**
     * creates a single leg information
     *
     * @param bool      $isFlexibleDate
     * @param \DateTime $departureAt
     * @param bool      $isReturn
     *
     * @return Leg
     */
    public static function buildLeg(bool $isFlexibleDate, \DateTime $departureAt, bool $isReturn = false) : Leg
    {
        $leg = new Leg();
        $leg->setDeparture('BER');
        $leg->setArrival('LON');
        if ($isReturn) {
            $leg->setDeparture('LON');
            $leg->setArrival('BER');
        }
        $leg->setDepartAt($departureAt);
        $leg->setIsFlexibleDate($isFlexibleDate);

        return $leg;
    }

    /**
     * builds the whole business case part of request
     *
     * @param array $options
     *
     * @return BusinessCase
     */
    public static function buildBusinessCase(array $options) : BusinessCase
    {
        $isAreaSearch = false;
        if (isset($options[self::OPT_AREA_SEARCH]) && $options[self::OPT_AREA_SEARCH] === true) {
            $isAreaSearch = true;
        }

        $type = 'one-way';
        if (isset($options[self::OPT_TYPE])) {
            $type = $options[self::OPT_TYPE];
        }
        $businessCaseOptions = new BusinessCaseOptions();
        $businessCaseOptions->setIsAreaSearch($isAreaSearch);
        if (isset($options[self::OPT_RESULT_LIMIT])) {
            $businessCaseOptions->setResultLimit($options[self::OPT_RESULT_LIMIT]);
        }
        $businessCaseOptions->setIsOvernight(false);
        if (isset($options[self::OPT_IS_OVERNIGHT])) {
            $businessCaseOptions->setIsOvernight($options[self::OPT_IS_OVERNIGHT]);
        }


        $businessCase = new BusinessCase();
        $businessCase->setOptions($businessCaseOptions);
        $businessCase->setType($type);

        return $businessCase;
    }

    /**
     * sets the pax information at the request out of given array
     * [ amountOfAdults, amountOfChildren, amountOfInfants ]
     *
     * @param Request $request
     * @param         $options
     */
    public static function setPax(Request $request, $options) : void
    {
        $request->setAdults(isset($options[self::OPT_PAX][0]) ? $options[self::OPT_PAX][0] : 0);
        $request->setChildren(isset($options[self::OPT_PAX][1]) ? $options[self::OPT_PAX][1] : 0);
        $request->setInfants(isset($options[self::OPT_PAX][2]) ? $options[self::OPT_PAX][2] : 0);
    }
}
