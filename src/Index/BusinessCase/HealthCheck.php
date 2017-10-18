<?php
namespace AmadeusService\Index\BusinessCase;

use AmadeusService\Application\BusinessCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HealthCheck
 * @package AmadeusService\Index\BusinessCase
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
