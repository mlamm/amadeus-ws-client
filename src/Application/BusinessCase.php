<?php
namespace Flight\Service\Amadeus\Application;

use Monolog\Logger;
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
        return $this->respond();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->application['monolog'];
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->application['config'];
    }
}
