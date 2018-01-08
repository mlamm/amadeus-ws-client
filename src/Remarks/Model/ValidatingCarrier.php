<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

use Doctrine\Common\Collections\Collection;
use Flight\Library\RemarksRequest\ResponseMapping\Entity\RemarksResponse\Carrier;
use Flight\Library\RemarksRequest\ResponseMapping\Entity\RemarksResponse\Carriers;

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

    public function addToCarriers(Carriers $carriers) : Carriers
    {
        if ($this->validatingCarrier !== null) {
            $carriers->setValidating(new Carrier());
            $carriers->getValidating()->setIata($this->validatingCarrier);
        }

        return $carriers;
    }
}
