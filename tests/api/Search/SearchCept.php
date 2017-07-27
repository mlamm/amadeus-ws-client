<?php 
$I = new ApiTester($scenario);
$I->wantTo('to search for offers based of sent parameters');
$I->sendPOST(
    '/search/',
    (array) json_decode(
        file_get_contents(
            __DIR__ . '/../../_support/fixtures/request.json'
        )
    )
);
$I->seeResponseIsJson();
$I->haveHttpHeader('content-type', 'application/hal+json');