<?php

/** @var ApiTester $I */
$I = new ApiTester($scenario);
$I->wantTo('see the itinerary read result as json+hal response');
$I->haveHttpHeader(
    'session',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-session-header.json'))
);
$I->haveHttpHeader(
    'authentication',
    file_get_contents(codecept_data_dir('requests/Itinerary/valid-auth-header.json'))
);
$I->sendGET('/itinerary/?recordLocator=QTDEOG');
$I->seeResponseCodeIs(200);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsHal();
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/itinerary/itinerary.json'));
$I->canSeeResponseIsJson();
$I->canSeeResponseContainsJson(
    json_decode(
        file_get_contents(
            codecept_data_dir('fixtures/response/itinerary/correct-response.json')
        ),
        true
    )
);
$I->seeResponseHasLinkToSelf('/itinerary');
