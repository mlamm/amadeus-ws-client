<?php
$I = new ApiTester($scenario);
$I->wantTo('see an error for authentication when the authentication node in the request is missing');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('not-authenticated-request.json'))
);
$I->seeResponseCodeIs(500);
$I->haveHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseContainsJson(
    [
        'code' => 'ARS0001',
        'message' => 'The `Amadeus\Client::securityAuthenticate` method didn\'t return state OK',
        'status' => 500
    ]
);
