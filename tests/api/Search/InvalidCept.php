<?php
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request is not correct if it could not be mapped and processed');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/invalid-request.json'))
);
$I->seeResponseCodeIs(400);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsValidErrorResponse();
$I->canSeeResponseContainsJson(
    [
        'code' => 'ARS0001',
        'message' => 'MALFORMED REQUEST',
        'status' => 400
    ]
);
$I->seeResponseHasLinkToSelf('/flight-search/');
