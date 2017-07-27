<?php
namespace AmadeusService\Index\BusinessCase;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Search\Response\HalResponse;

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
        return new HalResponse(null, 200);
    }
}