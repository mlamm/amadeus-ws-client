<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the defined schema if the request I send is valid');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/valid-request.json'))
);
$I->seeResponseCodeIs(200);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/response-schema.json'));
$I->seeResponseHasLinkToSelf('/flight-search/');
