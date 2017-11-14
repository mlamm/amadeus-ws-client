<?php
namespace Flight\Service\Amadeus\Index\BusinessCase;

use Flight\Service\Amadeus\Application\BusinessCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HealthCheck
 * @package Flight\Service\Amadeus\Index\BusinessCase
 */
class HealthCheck extends BusinessCase
{
    /**
     * @return Response
     */
    public function respond() : Response
    {
        $checkText = 'I am alive.';

        return new Response($checkText, 200, ['content-type' => 'text/plain']);
    }
}
