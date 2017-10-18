<?php
$I = new ApiTester($scenario);
$I->wantTo('expect to be forwarded to health-check endpoint when requesting service root');
$I->sendGET('/');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'text/plain');
$I->canSeeResponseContains('I am alive.');

$I->sendPOST('/');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'text/plain');
$I->canSeeResponseContains('I am alive.');
