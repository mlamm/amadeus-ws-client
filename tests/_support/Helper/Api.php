<?php
namespace Helper;

use Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends Module
{
    public function getResponseContent()
    {
        return $this->getModule('REST')->response;
    }
}
