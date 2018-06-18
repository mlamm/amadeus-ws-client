<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Model;

use Doctrine\Common\Collections\Collection;

/**
 * PaxFareDetails.php
 *
 * <Description>
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class PaxFareDetails
{
    private const PTC_CHILD = 'CH';
    private const PTC_ADULT = 'ADT';
    private const PTC_INFANT = 'INF';

    /**
     * @var float
     */
    private $taxPerPax;

    /**
     * @var float
     */
    private $farePerPax;

    /**
     * @var float
     */
    private $paymentFeesPerPax;

    /**
     * @var int
     */
    private $paxCount;

    /**
     * @param float $tax
     * @param float $fare
     * @param float $paymentFees
     * @param int   $paxCount
     */
    public function __construct(float $tax, float $fare, float $paymentFees, int $paxCount)
    {
        $this->taxPerPax = $tax;
        $this->farePerPax = $fare;
        $this->paymentFeesPerPax = $paymentFees;
        $this->paxCount = $paxCount;
    }

    /**
     * Build object for adult pax
     *
     * @param Collection $fareProducts
     * @return PaxFareDetails
     */
    public static function adultFromFareProducts(Collection $fareProducts)
    {
        return static::fromFareProducts($fareProducts, self::PTC_ADULT);
    }

    /**
     * Build object for child pax
     *
     * @param Collection $fareProducts
     * @return PaxFareDetails
     */
    public static function childFromFareProducts(Collection $fareProducts)
    {
        return static::fromFareProducts($fareProducts, self::PTC_CHILD);
    }

    /**
     * Build object for infant pax
     *
     * @param Collection $fareProducts
     * @return PaxFareDetails
     */
    public static function infantFromFareProducts(Collection $fareProducts)
    {
        return static::fromFareProducts($fareProducts, self::PTC_INFANT);
    }

    /**
     * Build object for a single pax type from a list of <paxFareProduct> nodes
     *
     * @param Collection $fareProducts
     * @param string     $ptc
     * @return static
     */
    private static function fromFareProducts(Collection $fareProducts, string $ptc)
    {
        $fareProduct = $fareProducts
            ->filter(
                function ($fareProduct) use ($ptc) {
                    return $fareProduct->paxReference->ptc === $ptc;
                }
            )->first();

        $monetaryDetail = $fareProduct ? MonetaryDetails::fromPaxFareProduct($fareProduct) : new MonetaryDetails([]);

        $fares = (float) ($monetaryDetail->getTotalWithoutTicketingFees() ?? $fareProduct->paxFareDetail->totalFareAmount ?? 0.0);
        $pax   = (float) ($fareProduct->paxFareDetail->totalTaxAmount ?? 0.0);
        $paymentFees = $monetaryDetail->getTicketingFeesTotal() ?? 0.0;

        $paxCount = 0;
        if ($fareProduct->paxReference->traveller ?? false) {
            $paxCount = count((array)$fareProduct->paxReference->traveller);
        }

        return new static($pax, $fares, $paymentFees, $paxCount);
    }

    public function getTaxPerPax(): float
    {
        return $this->taxPerPax;
    }

    public function getFarePerPax(): float
    {
        return $this->farePerPax;
    }

    public function getTotalTaxAmount(): float
    {
        return $this->taxPerPax * $this->paxCount;
    }

    public function getTotalFareAmount(): float
    {
        return $this->farePerPax * $this->paxCount;
    }

    public function getTotalPaymentFees(): float
    {
        return $this->paymentFeesPerPax * $this->paxCount;
    }

    public function getPaymentFeesPerPax(): float
    {
        return $this->paymentFeesPerPax;
    }
}
