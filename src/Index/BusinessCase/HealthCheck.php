<?php
namespace AmadeusService\Index\BusinessCase;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Response\HalResponse;

/**
 * Class HealthCheck
 * @package AmadeusService\Index\BusinessCase
 */
class HealthCheck extends BusinessCase
{
    /**
     * @return HalResponse
     */
    public function respond()
    {
        return new HalResponse(
            [
                '_links' => [
                    'search' => '/flight-search/'
                ],
                'state' => 'alive',
                'database' => [
                    'ibe' => $this->get('database.ibe')->ping() ? 'alive' : 'dead'
                ]
            ],
            200
        );
    }
}