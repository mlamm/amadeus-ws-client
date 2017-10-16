<?php
declare(strict_types=1);

namespace AmadeusService\Search\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Nights.php
 *
 * Calculate the number of nights
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Nights
{
    /**
     * @param Collection $segments
     * @return int
     */
    public static function calc(Collection $segments) : int
    {
        if ($segments->count() === 0) {
            return 0;
        }

        /** @var \DateTime $departure */
        $departure = clone $segments->first()->getDepartAt();
        $departure->setTime(0, 0, 0);
        /** @var \DateTime $arrival */
        $arrival = clone $segments->last()->getArriveAt();
        $arrival->setTime(0, 0, 0);

        return (int) (($arrival->getTimestamp() - $departure->getTimestamp()) / 86400);
    }
}
