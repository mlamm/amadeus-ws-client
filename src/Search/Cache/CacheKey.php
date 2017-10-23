<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Search\Cache;

use Flight\SearchRequestMapping\Entity\BusinessCase;
use Flight\SearchRequestMapping\Entity\Leg;
use Flight\SearchRequestMapping\Entity\Request;

/**
 * CacheKey.php
 *
 * Builds the cache key from all required inputs
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class CacheKey
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param Request      $request
     * @param BusinessCase $businessCase
     * @param \stdClass    $config
     */
    public function __construct(Request $request, BusinessCase $businessCase, \stdClass $config)
    {
        $this->key = $this->createCacheKey($request, $businessCase, $config);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->key;
    }

    /**
     * @param Request      $request
     * @param BusinessCase $businessCase
     * @param \stdClass    $config
     * @return string
     */
    private function createCacheKey(Request $request, BusinessCase $businessCase, \stdClass $config) : string
    {
        $filterCabinClass = $request->getFilterCabinClass() ?: [];
        sort($filterCabinClass);

        $values = [
            'filter-cabin-class' => $filterCabinClass,
            'filter-airline'     => $request->getFilterAirline(),
            'adults'             => $request->getAdults(),
            'children'           => $request->getChildren(),
            'infants'            => $request->getInfants(),
            'is-area-search'     => $businessCase->getOptions()->isAreaSearch(),
            'is-overnight'       => $businessCase->getOptions()->isOvernight(),
            'legs'               => $request->getLegs()->map(function (Leg $leg) {
                return [
                    'departure'        => $leg->getDeparture(),
                    'arrival'          => $leg->getArrival(),
                    'depart-at'        => $leg->getDepartAt()->format('Y-m-d'),
                    'is-flexible-date' => $leg->getIsFlexibleDate(),
                ];
            })->toArray(),
            'additional-entropy' => [
                'office-id'         => $businessCase->getAuthentication()->getOfficeId(),
                'user-id'           => $businessCase->getAuthentication()->getUserId(),
                'excluded-airlines' => $config->excluded_airlines ?? null,
                'request-options'   => $config->request_options ?? null,
            ],
        ];

        ksort($values);
        return md5(json_encode($values)) . 'AMA';
    }
}
