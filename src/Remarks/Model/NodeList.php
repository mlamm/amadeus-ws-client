<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * NodeList.php
 *
 * Reliably make a list from response nodes (which may already be an array or only a single object)
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class NodeList extends ArrayCollection
{
    /**
     * @param mixed $elements
     */
    public function __construct($elements = [])
    {
        if ($elements === null) {
            $elements = [];
        }

        if (!is_array($elements)) {
            $elements = [$elements];
        }

        parent::__construct($elements);
    }
}
