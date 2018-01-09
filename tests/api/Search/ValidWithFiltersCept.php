<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the response schema even if I set all supported filters');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/valid-request-with-filters.json'))
);
$I->seeResponseCodeIs(200);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/response-schema.json'));
$I->seeResponseHasLinkToSelf('/flight-search/');