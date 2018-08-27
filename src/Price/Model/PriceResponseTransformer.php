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
                // %TODO, when is this being used? TEST 1 pax
                $price->setValidatingCarrier(
                    (string) $response->fareList->validatingCarrier
                        ->carrierInformation
                        ->carrierCode
                );
                $tktDate = '0000-00-00';
                foreach ($fareList['lastTktDate'] as $lastTktDate) {
                    if ($lastTktDate->businessSemantic == 'LT') {
                        $tktDate = $lastTktDate->dateTime->year
                            . '-' . $lastTktDate->dateTime->month
                            . '-' . $lastTktDate->dateTime->day;
                    }
                };
                $price->setLastTicketingDate(date('Y-m-d', strtotime($tktDate)));
                $price->setValidatingCarrier(
                    (string) $fareList['validatingCarrier']
                        ->carrierInformation
                        ->carrierCode
                );
                $passengerPrice->add($this->mapPassengerPrice((object) $fareList));
            } else {
                $passengerPrice = new ArrayCollection();

                // 1 PAX - QLWH2V
                // 4 PAX - PGV84B
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

                $price->setPassengerPrice($passengerPrice);
            }
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

        foreach ($fare->taxInformation as $taxData) {
            $totalTax += (float) $taxData->amountDetails->fareDataMainInformation->fareAmount;
        }

        $price->setTotalTax(round($totalTax, 2));
        $price->setPassengerRef($this->getPassengerRef($fare));

        foreach ($fare->fareDataInformation as $baseFare) {
            if (is_array($baseFare)) {
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
     * @param \stdClass $fare
     * @return ArrayCollection
     */
    private function getPassengerRef(\stdClass $fare): ArrayCollection
    {
        $passengerRefs = new ArrayCollection();

        foreach ($fare->paxSegReference->refDetails as $refDetail) {
            $passengerRef = new ArrayCollection([
                'qualifier' => $refDetail->refQualifier,
                'number'    => $refDetail->refNumber
            ]);
            $passengerRefs->add($passengerRef);
        }

        return $passengerRefs;
    }
}
