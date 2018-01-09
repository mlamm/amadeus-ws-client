<?php
$I = new ApiTester($scenario);
$I->wantTo('expect the health check endpoint to return a information about the state of the application and db');
$I->sendGET('/health');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'text/plain');
$I->canSeeResponseContains('I am alive.');

$I->sendPOST('/health');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'text/plain');
$I->canSeeResponseContains('I am alive.');