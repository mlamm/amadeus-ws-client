<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Doctrine\Common\Collections\Collection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Carrier;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\LegCarriers;

/**
 * ValidatingCarrier.php
 *
 * Fetch the validating carrier from the <paxFareProducts> node
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ValidatingCarrier
{
    /**
     * @var string
     */
    private $validatingCarrier;

    public function __construct(Collection $fareProducts)
    {
        $paxFareDetails = new NodeList($fareProducts->first()->paxFareDetail ?? []);
        $codeShareDetails = new NodeList($paxFareDetails->first()->codeShareDetails ?? []);

        if (isset($codeShareDetails->first()->company)) {
            $this->validatingCarrier = (string) $codeShareDetails->first()->company;
        }
    }

    public function addToCarriers(LegCarriers $carriers) : LegCarriers
    {
        if ($this->validatingCarrier !== null) {
            $carriers->setValidating(new Carrier());
            $carriers->getValidating()->setIata($this->validatingCarrier);
        }

        return $carriers;
    }
}
