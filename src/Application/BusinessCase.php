<?php
namespace AmadeusService\Application;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class BusinessCase implements BusinessCaseInterface
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Invoke method
     *
     * @param Application $application
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Application $application, Request $request)
    {
        $this->application = $application;
        $this->request = $request;
        return $this->responds();
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }
}