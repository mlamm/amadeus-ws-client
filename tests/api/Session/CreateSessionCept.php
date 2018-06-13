<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the defined schema if the request I send is valid');
$I->haveHttpHeader('authentication', '{"office-id":"OFFICE","duty-code":"AA","user-id":"USER","password-data":"password=","password-length":"8","organization":"CC-ABCDEF"}');
$I->sendPOST('/session/create');

$I->seeResponseCodeIs(200);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/session/create.json'));
$I->seeResponseHasLinkToSelf('/session');