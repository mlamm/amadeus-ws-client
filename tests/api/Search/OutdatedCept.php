<?php
$I = new ApiTester($scenario);
$I->wantTo('see an error when requested offers for past dates');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/outdated-request.json'))
);
$I->seeResponseCodeIs(500);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseContainsJson(
    [
        'code' => 'ARS0004',
        'message' => 'AMADEUS RESPONSE ERROR [920,Past date/time not allowed]',
        'status' => 500
    ]
);
$I->seeResponseHasLinkToSelf('/flight-search/');