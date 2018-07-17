<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Search\Model\PaxFareDetails;

/**
 * PaxFareDetailsTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\PaxFareDetails
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class PaxFareDetailsTest extends \Codeception\Test\Unit
{
    /**
     * Does the object return the values given in the constructor?
     * Does it calculate totals?
     */
    public function testItReturnsGivenValues()
    {
        $object = new PaxFareDetails(1.0, 2.0, 3.0, 4);
        $this->assertSame(1.0, $object->getTaxPerPax());
        $this->assertSame(2.0, $object->getFarePerPax());
        $this->assertSame(3.0, $object->getPaymentFeesPerPax());

        $this->assertSame(4.0, $object->getTotalTaxAmount());
        $this->assertSame(8.0, $object->getTotalFareAmount());
        $this->assertSame(12.0, $object->getTotalPaymentFees());
    }

    /**
     * Does the named constructor build the object if there are <monetaryDetails> nodes?
     */
    public function testItBuildsFromFareProductsWithMonetaryDetails()
    {
        $fareProducts = [
            (object) [
                'paxReference' => (object) [
                    'ptc' => 'ADT',
                    'traveller' => [
                        (object) [],
                        (object) [],
                    ]
                ],
                'paxFareDetail' => (object) [
                    'totalFareAmount' => 123.0,
                    'totalTaxAmount' => 44.0,
                    'monetaryDetails' => [
                        (object) [
                            'amountType' => 'XOB', // totalWithoutTicketingFees
                            'amount' => 100.0
                        ],
                        (object) [
                            'amountType' => 'OB', // totalTicketingFees
                            'amount' => 23.0
                        ],
                    ],
                ]
            ],
        ];

        $object = PaxFareDetails::adultFromFareProducts(new ArrayCollection($fareProducts));
        $this->assertSame(44.0, $object->getTaxPerPax());
        $this->assertSame(56.0, $object->getFarePerPax());
        $this->assertSame(23.0, $object->getPaymentFeesPerPax());
    }

    /**
     * Does the named constructor build the object if there are NO <monetaryDetails> nodes?
     */
    public function testItBuildsFromFareProductsWithoutMonetaryDetails()
    {
        $fareProducts = [
            (object) [
                'paxReference' => (object) [
                    'ptc' => 'ADT',
                    'traveller' => [
                        (object) [],
                        (object) [],
                    ]
                ],
                'paxFareDetail' => (object) [
                    'totalFareAmount' => 123.0,
                    'totalTaxAmount' => 44.0,
                ]
            ],
        ];

        $object = PaxFareDetails::adultFromFareProducts(new ArrayCollection($fareProducts));
        $this->assertSame(44.0, $object->getTaxPerPax());
        $this->assertSame(79.0, $object->getFarePerPax());
        $this->assertSame(0.0, $object->getPaymentFeesPerPax());
    }

    /**
     * Does it distinguish between adults, children and infants?
     */
    public function testItSeparatesPaxTypes()
    {
        $fareProducts = new ArrayCollection([
            (object) [
                'paxReference' => (object) [
                    'ptc' => 'ADT',
                    'traveller' => [
                        (object) [],
                    ]
                ],
                'paxFareDetail' => (object) [
                    'totalFareAmount' => 2.0,
                    'totalTaxAmount' => 1.0,
                ]
            ],
            (object) [
                'paxReference' => (object) [
                    'ptc' => 'CH',
                    'traveller' => [
                        (object) [],
                    ]
                ],
                'paxFareDetail' => (object) [
                    'totalFareAmount' => 3.0,
                    'totalTaxAmount' => 2.0,
                ]
            ],
            (object) [
                'paxReference' => (object) [
                    'ptc' => 'INF',
                    'traveller' => [
                        (object) [],
                    ]
                ],
                'paxFareDetail' => (object) [
                    'totalFareAmount' => 4.0,
                    'totalTaxAmount' => 3.0,
                ]
            ],
        ]);

        $adults = PaxFareDetails::adultFromFareProducts($fareProducts);
        $children = PaxFareDetails::childFromFareProducts($fareProducts);
        $infants = PaxFareDetails::infantFromFareProducts($fareProducts);

        $this->assertSame(1.0, $adults->getFarePerPax());
        $this->assertSame(1.0, $children->getFarePerPax());
        $this->assertSame(1.0, $infants->getFarePerPax());
    }

    /**
     * Does it return zeros for missing passenger types without notices or warnings?
     */
    public function testItGivesDefaultsOnMissingPaxTypes()
    {
        $fareProducts = new ArrayCollection([]);

        $object = PaxFareDetails::adultFromFareProducts($fareProducts);

        $this->assertSame(0.0, $object->getTaxPerPax());
        $this->assertSame(0.0, $object->getFarePerPax());
        $this->assertSame(0.0, $object->getPaymentFeesPerPax());

        $this->assertSame(0.0, $object->getTotalTaxAmount());
        $this->assertSame(0.0, $object->getTotalFareAmount());
        $this->assertSame(0.0, $object->getTotalPaymentFees());
    }
}
