<?php

declare(strict_types=1);

use Codeception\Util\HttpCode;
use Flight\Service\Amadeus\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required parameter in options field contained by the business-case field');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/no-required-bc-options.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
$I->seeResponseIsValidErrorResponse();

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-one-way-combination' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-one-way-combination must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-overnight' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-overnight must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-area-search' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-area-search must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-benchmark' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-benchmark must be provided, but does not exist',
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
