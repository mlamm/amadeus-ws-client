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
$I->sendPOST(
    '/price/',
    file_get_contents(codecept_data_dir('requests/Price/create-price-tarif.json'))
);
$I->seeResponseCodeIs(204);
$I->seeHttpHeader('content-type', 'text/html; charset=UTF-8');