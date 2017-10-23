<?php

use Codeception\Util\HttpCode;
use AmadeusService\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain allowed values for predefined parameters');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/wrong-fixed-values.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['agent' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - agent must be in the defined set of values',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.content-provider' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - content-provider must be equal to "amadeus"',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - type must be in the defined set of values',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    [
        '_links' => [
            'self' => [
                'href' => '/flight-search/'
            ]
        ],
    ]
);
