<?php
$I = new ApiTester($scenario);
$I->wantTo('receive an ARS0001 response if authentication is missing');
$I->sendPOST(
    '/search/',
    file_get_contents(
        __DIR__ . '/../../_support/fixtures/empty-authentication-request.json'
    )
);
$I->seeResponseCodeIs(500);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseContains('{"errors":{"search":[{"code":"ARS0001","message":"The `Amadeus\\\Client::securityAuthenticate` method didn\u0027t return state OK","status":500}]}}');
