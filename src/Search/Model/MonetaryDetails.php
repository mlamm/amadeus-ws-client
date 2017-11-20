<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

/**
 * MonetaryDetails.php
 *
 * Make the content of the recommendation/recPriceInfo/monetaryDetail nodes accessible
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class MonetaryDetails
{
    /**
     *  Total amount without OB fees
     */
    private const AMOUNT_TYPE_XOB = 'XOB';

    /**
     * Ticketing fees total
     */
    private const AMOUNT_TYPE_OB = 'OB';

    /**
     * @var \stdClass
     */
    private $monetaryDetails;

    /**
     * Build object from a list of <monetaryDetail> nodes
     *
     * @param iterable $monetaryDetails
     */
    public function __construct(iterable $monetaryDetails)
    {
        foreach ($monetaryDetails as $detail) {
            $key = $detail->amountType ?? '';
            $this->monetaryDetails[$key] = $detail;
        }
    }

    /**
     * Build object from the <recommendation> node
     *
     * @param \stdClass $recommendation
     *
     * @return static
     */
    public static function fromRecommendation(\stdClass $recommendation): self
    {
        if (!isset($recommendation->recPriceInfo->monetaryDetail)) {
            return new static([]);
        }

        return new static(new NodeList($recommendation->recPriceInfo->monetaryDetail));
    }

    /**
     * Build object form a <paxFareProduct> node
     *
     * @param \stdClass $paxFareProduct
     *
     * @return MonetaryDetails
     */
    public static function fromPaxFareProduct(\stdClass $paxFareProduct): self
    {
        if (!isset($paxFareProduct->paxFareDetail->monetaryDetails)) {
            return new static([]);
        }

        return new static(new NodeList($paxFareProduct->paxFareDetail->monetaryDetails));
    }

    /**
     * True if there is a ticketing fee
     *
     * @return bool
     */
    public function hasTicketingFeesTotal(): bool
    {
        return isset($this->monetaryDetails[self::AMOUNT_TYPE_OB]->amount);
    }

    /**
     * Return value of the total ticketing fees
     *
     * @return float
     */
    public function getTicketingFeesTotal(): ?float
    {
        if (!$this->hasTicketingFeesTotal()) {
            return null;
        }

        return (float) $this->monetaryDetails[self::AMOUNT_TYPE_OB]->amount;
    }

    /**
     * True if there is a total fare without ticketing fees
     *
     * @return bool
     */
    public function hasTotalWithoutTicketingFees(): bool
    {
        return isset($this->monetaryDetails[self::AMOUNT_TYPE_XOB]->amount);
    }

    /**
     * Return the total fare without ticketing fee
     *
     * @return float|null
     */
    public function getTotalWithoutTicketingFees(): ?float
    {
        if (!$this->hasTotalWithoutTicketingFees()) {
            return null;
        }

        return (float) $this->monetaryDetails[self::AMOUNT_TYPE_XOB]->amount;
    }
}
