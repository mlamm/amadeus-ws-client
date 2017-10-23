<?php

declare(strict_types=1);

use Codeception\Util\HttpCode;
use AmadeusService\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required fields in the authentication part of business-case field');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/no-required-bc-auth.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.office-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.office-id must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.user-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.user-id must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-data' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-data must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-length' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-length must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.duty-code' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.duty-code must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.organization-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.organization-id must be provided, but does not exist',
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
