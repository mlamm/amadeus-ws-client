<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the defined schema if the request I send is valid');
$I->haveHttpHeader(
    'session',
    file_get_contents(codecept_data_dir('requests/Price/valid-session-header.json'))
);
$I->haveHttpHeader(
    'authentication',
    file_get_contents(codecept_data_dir('requests/Price/valid-auth-header.json'))
);
$I->haveHttpHeader('Content-Type', 'application/json');
$I->sendGET('/price/');
$I->seeResponseCodeIs(200);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsHal();
$I->seeResponseHasLinkToSelf('/price/');
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/price/get-response-schema.json'));