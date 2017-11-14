<?php

/* @var $scenario  \Codeception\Scenario */

use Codeception\Util\HttpCode;

$I = new ApiTester($scenario);
$I->wantTo('see the response in case of a warning in the service');
$I->sendGET('/throwup/warning');

$I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
$I->seeResponseIsHal();
$I->seeResponseIsValidErrorResponse();
