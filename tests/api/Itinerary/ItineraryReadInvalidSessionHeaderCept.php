<?php

/** @var ApiTester $I */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the session missed a param');
$I->haveHttpHeader(
    'Session',
    file_get_contents(codecept_data_dir('requests/Itinerary/invalid-session-header.json'))
);
$I->haveHttpHeader(
    'Authenticate',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-auth-header.json'))
);
$I->sendGET('/itinerary/?recordLocator=QTDEOG');
$I->seeResponseCodeIs(400);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsValidErrorResponse();
$I->canSeeResponseIsJson();
$I->canSeeResponseContainsJson(
    json_decode(
        file_get_contents(
            codecept_data_dir('requests/Itinerary/missing-request-session-param-response.json')
        ),
        true
    )
);
$I->seeResponseHasLinkToSelf('/itinerary');
