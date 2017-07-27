<?php 
$I = new ApiTester($scenario);
$I->wantTo('expect the health check endpoint to return an empty JSON response and a 200 OK');
$I->sendGET('/_hc');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'application/hal+json');
