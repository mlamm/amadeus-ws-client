<?php

/* @var $scenario  \Codeception\Scenario */

use Codeception\Util\HttpCode;

$scenario->skip('unable to handle this error');

$I = new ApiTester($scenario);
$I->wantTo('see the response in case of a compile error in the service');
$I->sendGET('/throwup/compile-error');

$I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
$I->seeResponseIsHal();
$I->seeResponseIsValidErrorResponse();
