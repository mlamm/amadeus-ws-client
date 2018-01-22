<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse;

/**
 * Carriers.php
 *
 * Extract carrier information
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Carriers
{
    public static function writeToSegment(
        SearchResponse\Segment $segment,
        \stdClass $flightDetail,
        \ArrayAccess $companyTextIndex
    ): SearchResponse\Segment {

        $carriers = new SearchResponse\SegmentCarriers();
        $carriers->setMarketing(self::fetchMarketing($flightDetail));
        $carriers->setOperating(
            self::fetchOperatingFromFlightDetail($flightDetail)
            ?: self::fetchOperatingFromCompanyText($companyTextIndex, $flightDetail)
            ?: self::createEmptyCarrier()
        );

        $segment->setCarriers($carriers);

        return $segment;
    }

    private static function fetchOperatingFromFlightDetail(\stdClass $flightDetail): ?SearchResponse\Carrier
    {
        if (!isset($flightDetail->flightInformation->companyId->operatingCarrier)) {
            return null;
        }

        $carrier = new SearchResponse\Carrier();
        $carrier->setIata($flightDetail->flightInformation->companyId->operatingCarrier);

        return $carrier;
    }

    /**
     * @param \ArrayAccess|string[] $companyIndex
     * @param \stdClass             $flightDetail
     *
     * @return SearchResponse\Carrier|null
     */
    private static function fetchOperatingFromCompanyText(\ArrayAccess $companyIndex, \stdClass $flightDetail): ?SearchResponse\Carrier
    {
        $textRefNumber = $flightDetail->commercialAgreement->codeshareDetails->flightNumber ?? null;

        if (!isset($companyIndex[$textRefNumber])) {
            return null;
        }

        $carrier = new SearchResponse\Carrier();
        $carrier->setIata('');
        $carrier->setName($companyIndex[$textRefNumber]);

        return $carrier;
    }

    /**
     * @return null
     */
    private static function createEmptyCarrier()
    {
        return null;
    }

    /**
     * @param \stdClass $flightDetail
     *
     * @return SearchResponse\Carrier
     */
    private static function fetchMarketing(\stdClass $flightDetail): SearchResponse\Carrier
    {
        $carrier = new SearchResponse\Carrier();
        $carrier->setIata($flightDetail->flightInformation->companyId->marketingCarrier);

        return $carrier;
    }
}
