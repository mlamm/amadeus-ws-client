<?php

namespace Flight\Service\Amadeus\Price\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Price\Response\PriceGetResponse;

/**
 * PriceResponseTransformer class.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class PriceResponseTransformer
{
    /**
     * @param \stdClass $response
     *
     * @return PriceGetResponse
     */
    public function mapResult(\stdClass $response) : PriceGetResponse
    {
        $responseResult = new PriceGetResponse();
        $price          = new Price();

        // no TST exists -- "NO TST RECORD EXISTS"
        if (!empty($response->fareList)) {
            $fareList       = (array) $response->fareList;
            $passengerPrice = new ArrayCollection();

            if (array_key_exists('pricingInformation', $fareList)) {
                $tktDate = null;
                foreach ($fareList['lastTktDate'] as $lastTktDate) {
                    if ($lastTktDate->businessSemantic == 'LT') {
                        $tktDate = $lastTktDate->dateTime->year
                            . '-' . $lastTktDate->dateTime->month
                            . '-' . $lastTktDate->dateTime->day;
                    }
                }

                if ($tktDate === null) {
                    $price->setLastTicketingDate('0000-00-00');
                } else {
                    $price->setLastTicketingDate(date('Y-m-d', strtotime($tktDate)));
                }

                if (isset($fareList['validatingCarrier'])) {
                    $price->setValidatingCarrier(
                        (string)$fareList['validatingCarrier']
                            ->carrierInformation
                            ->carrierCode
                    );
                }
                $passengerPrice->add($this->mapPassengerPrice((object) $fareList));
            } else {
                $passengerPrice = new ArrayCollection();

                foreach ($fareList as $fare) {
                    $tktDate = '0000-00-00';
                    if (!empty($fare->lastTktDate)) {
                        foreach ($fare->lastTktDate as $lastTktDate) {
                            if ($lastTktDate->businessSemantic == 'LT') {
                                $tktDate = $lastTktDate->dateTime->year
                                    . '-' . $lastTktDate->dateTime->month
                                    . '-' . $lastTktDate->dateTime->day;
                            }
                        };
                    }

                    $price->setLastTicketingDate(date('Y-m-d', strtotime($tktDate)));

                    if (!empty($fare->validatingCarrier)) {
                        $price->setValidatingCarrier(
                            (string) $fare->validatingCarrier
                                ->carrierInformation
                                ->carrierCode
                        );
                    }

                    $passengerPrice->add($this->mapPassengerPrice($fare));
                }
            }

            $price->setPassengerPrice($passengerPrice);
        }

        $responseResult->setResult($price);
        return $responseResult;
    }

    /**
     * map passenger price data
     *
     * @param $fare
     *
     * @return PassengerPrice
     */
    public function mapPassengerPrice($fare) : PassengerPrice
    {
        $price    = new PassengerPrice();
        $totalTax = 0.00;

        if (isset($fare->taxInformation)) {
            // if taxInformation is an array, we need to loop all elements,
            // if it's already the element itself we just process it
            if (\is_array($fare->taxInformation)) {
                foreach ($fare->taxInformation as $taxData) {
                    $totalTax += (float) $taxData->amountDetails->fareDataMainInformation->fareAmount;
                }
            } else {
                $totalTax += (float) $fare->taxInformation->amountDetails->fareDataMainInformation->fareAmount;
            }
        }

        $price->setTotalTax(round($totalTax, 2));
        $price->setPassengerRef($this->getPassengerRef($fare));

        foreach ($fare->fareDataInformation as $baseFare) {
            if (\is_array($baseFare)) {
                foreach ($baseFare as $base) {
                    if ($base->fareDataQualifier == 'B') {
                        $price->setBaseFare((float) $base->fareAmount);
                        $price->setBaseFareCurrency($base->fareCurrency);
                    } elseif ($base->fareDataQualifier == 'E') {
                        $price->setEquivFare((float) $base->fareAmount);
                        $price->setEquivFareCurrency($base->fareCurrency);
                    }
                }
            }
        }

        return $price;
    }

    /**
     * Build passenger-reference element and return it.
     *
     * @param \stdClass $fare input fare element from gds
     * @return ArrayCollection
     */
    private function getPassengerRef(\stdClass $fare): ArrayCollection
    {
        $passengerRefs = new ArrayCollection();

        // support various formats, if there are multiple pax, this will be an array
        if (is_array($fare->paxSegReference->refDetails)) {
            foreach ($fare->paxSegReference->refDetails as $refDetail) {
                $passengerRef = new ArrayCollection([
                    'qualifier' => $refDetail->refQualifier,
                    'id'        => $refDetail->refNumber
                ]);
                $passengerRefs->add($passengerRef);
            }
        } else {
            $passengerRef = new ArrayCollection([
                'qualifier' => $fare->paxSegReference->refDetails->refQualifier,
                'id'        => $fare->paxSegReference->refDetails->refNumber
            ]);
            $passengerRefs->add($passengerRef);
        }

        return $passengerRefs;
    }
}
