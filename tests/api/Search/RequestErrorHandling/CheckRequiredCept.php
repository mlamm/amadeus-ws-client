<?php

use Codeception\Util\HttpCode;
use Flight\Service\Amadeus\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required parameter');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/no-required-fields.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['adults' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - adults must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['children' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - children must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['infants' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - infants must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['legs' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - legs must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['business-cases' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - business-cases must be provided, but does not exist',
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
