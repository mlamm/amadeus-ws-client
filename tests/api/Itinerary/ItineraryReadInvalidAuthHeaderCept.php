<?php

/** @var ApiTester $I */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the authentication is not valid');
$I->haveHttpHeader(
    'session',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-session-header.json'))
);
$I->haveHttpHeader(
    'authentication',
    file_get_contents(codecept_data_dir('requests/Itinerary/invalid-auth-header.json'))
);
$I->sendGET('/itinerary/?recordLocator=QTDEOG');
$I->seeResponseCodeIs(400);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsValidErrorResponse();
$I->canSeeResponseIsJson();
$I->canSeeResponseContainsJson(
    json_decode(
        file_get_contents(
            codecept_data_dir('fixtures/response/itinerary/missing-request-auth-param-response.json')
        ),
        true
    )
);
$I->seeResponseHasLinkToSelf('/itinerary');