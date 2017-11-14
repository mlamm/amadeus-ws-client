<?php

/* @var $scenario  \Codeception\Scenario */

use Codeception\Util\HttpCode;

$I = new ApiTester($scenario);
$I->wantTo('see the response in case of a notice in the service');
$I->sendGET('/throwup/notice');

$I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
$I->seeResponseIsHal();
$I->seeResponseIsValidErrorResponse();
