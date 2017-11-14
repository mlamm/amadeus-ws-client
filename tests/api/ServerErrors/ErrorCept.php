<?php

/* @var $scenario  \Codeception\Scenario */

use Codeception\Util\HttpCode;

$I = new ApiTester($scenario);
$I->wantTo('see the response in case of an error in the service');
$I->sendGET('/throwup/error');

$I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
$I->seeResponseIsHal();
$I->seeResponseIsValidErrorResponse();
