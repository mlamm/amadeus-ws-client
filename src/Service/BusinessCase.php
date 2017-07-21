<?php
namespace AmadeusService\Service;

use Silex\Application;

abstract class BusinessCase
{
    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
        return $this->respond();
    }

    public function __invoke(Application $application)
    {
        return new {self::class}($application);
    }

    public function application()
    {
        return $this->application;
    }

    abstract protected function respond();
}