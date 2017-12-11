<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Location;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Segment;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\TechnicalStop;

/**
 * TechnicalStops.php
 *
 * Build technical stop entity from the flightDetails of a segment
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class TechnicalStops
{
    public static function writeToSegment(Segment $segment, \stdClass $flightDetails): Segment
    {
        if (!isset($flightDetails->technicalStop)) {
            return $segment;
        }

        $technicalStopNodes = new NodeList($flightDetails->technicalStop);
        $segment->setTechnicalStops(new ArrayCollection());

        foreach ($technicalStopNodes as $stopNode) {
            $stopEntity = new TechnicalStop();

            foreach (new NodeList($stopNode->stopDetails) as $stopDetail) {
                if (isset($stopDetail->dateQualifier, $stopDetail->date, $stopDetail->firstTime)) {
                    $dateTime = DateTime::fromDateAndTime($stopDetail->date, $stopDetail->firstTime);

                    if ($stopDetail->dateQualifier === 'AD') {
                        $stopEntity->setDepartAt($dateTime);
                    } elseif ($stopDetail->dateQualifier === 'AA') {
                        $stopEntity->setArriveAt($dateTime);
                    }
                }

                if (isset($stopDetail->locationId)) {
                    $stopEntity->setAirport(new Location());
                    $stopEntity->getAirport()->setIata($stopDetail->locationId);
                }
            }

            $segment->getTechnicalStops()->add($stopEntity);
        }

        return $segment;
    }
}
