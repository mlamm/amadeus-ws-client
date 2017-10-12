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
     * @var int
     */
    private $nights = 0;

    /**
     * @param Collection $segments
     */
    public function __construct(Collection $segments)
    {
        if ($segments->count() > 0) {
            /** @var \DateTime $departure */
            $departure = clone $segments->first()->getDepartAt();
            $departure->setTime(0, 0, 0);
            /** @var \DateTime $arrival */
            $arrival = clone $segments->last()->getArriveAt();
            $arrival->setTime(0, 0, 0);

            $this->nights = (int) (($arrival->getTimestamp() - $departure->getTimestamp()) / 86400);
        }
    }

    /**
     * @return int
     */
    public function getNights() : int
    {
        return $this->nights;
    }
}
