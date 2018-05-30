<?php
namespace Flight\Service\Amadeus\Session\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Flight\Service\Amadeus\Application\Response\HalResponse;

/**
 * Class CreateSession BusinessCase
 *
 * @package Flight\Service\Amadeus\Session\BusinessCase
 */
class CreateSession extends BusinessCase
{

    /**
     * Method to define what the business case returns.
     *
     * @return HalResponse
     */
    public function respond()
    {
        return new HalResponse(null, 200);
    }


}
