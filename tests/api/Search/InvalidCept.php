<?php
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request is not correct if it could not be mapped and processed');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('invalid-request.json'))
);
$I->seeResponseCodeIs(400);
$I->haveHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseContainsJson(
    [
        'code' => 'ARS0003',
        'message' => 'The provided request could not be mapped into the appropriate format',
        'status' => 400
    ]
);
