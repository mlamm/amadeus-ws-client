<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the defined schema if the request I send is valid');
$I->haveHttpHeader('authentication', '{"office-id":"OFFICE","duty-code":"AA","user-id":"USER","password-data":"password=","password-length":"8","organization":"CC-ABCDEF"}');
$I->haveHttpHeader('session', '{"session_id": "004SB0YI0D", "sequence_number": 1, "security_token": "CDNG61BJZY626FALIMOV1NW0"}');
$I->sendPOST('/session/terminate');

$I->seeResponseCodeIs(204);
$I->seeHttpHeader('content-type', 'text/html; charset=UTF-8');
$I->canSeeResponseIsValidOnSchemaFile(codecept_data_dir('schema/session/terminate.json'));
$I->seeResponseHasLinkToSelf('/session');