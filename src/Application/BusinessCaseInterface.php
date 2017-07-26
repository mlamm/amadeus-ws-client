<?php
namespace AmadeusService\Application;

use Symfony\Component\HttpFoundation\Response;

interface BusinessCaseInterface
{
    /**
     * Method that defines the response of a business case
     *
     * @return Response
     */
    public function respond();
}