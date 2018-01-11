<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Amadeus\Client\Result;

/**
 * CompanyTextIndex.php
 *
 * Holds the company text strings indexed by their id
 *
 * @copyright Copyright (c) 2018 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CompanyTextIndex extends \ArrayObject
{
    public static function fromSearchResult(Result $result): self
    {
        $index = [];

        foreach (new NodeList($result->response->companyIdText ?? []) as $companyIdText) {
            $index[$companyIdText->textRefNumber] = $companyIdText->companyText;
        }

        return new static($index);
    }
}
