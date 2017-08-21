<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an response that matches the defined schema if the request I send is valid');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('valid-request.json'))
);
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'application/hal+json');

$response = $I->grabResponse();

$validator = new \JsonSchema\Validator();
$validator->validate(
    $response,
    (object)[
        '$ref' => 'file://' . getcwd() . '/docs/payload/response-schema.json'
    ]
);

$I->expect($validator->isValid());