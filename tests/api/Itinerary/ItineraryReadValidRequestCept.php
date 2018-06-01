<?php

/** @var ApiTester $I */
$I = new ApiTester($scenario);
$I->wantTo('see the itinerary read result as json+hal request');
$I->haveHttpHeader(
    'Session',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-session-header.json'))
);
$I->haveHttpHeader(
    'Authenticate',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-auth-header.json'))
);
$I->sendGET('/itinerary/?recordLocator=QTDEOG');
print_r($I->grabResponse());die;
$I->seeResponseCodeIs(500);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsValidErrorResponse();
$I->canSeeResponseIsJson();
$I->canSeeResponseContainsJson(
    json_decode(
        file_get_contents(
            codecept_data_dir('requests/Itinerary/response.json')
        ),
        true
    )
);
$I->seeResponseHasLinkToSelf('/itinerary');
