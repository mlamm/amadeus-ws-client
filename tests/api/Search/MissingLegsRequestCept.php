<?php 
$I = new ApiTester($scenario);
$I->wantTo('receive an ARS0002 response if legs are missing');
$I->sendPOST(
    '/search/',
    file_get_contents(
        __DIR__ . '/../../_support/fixtures/empty-leg-request.json'
    )
);
$I->seeResponseCodeIs(500);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseContains('{"errors":{"search":[{"code":"ARS0002","message":"The provided search parameters do not suffice the necessary data to start a new search","status":500}]}}');
