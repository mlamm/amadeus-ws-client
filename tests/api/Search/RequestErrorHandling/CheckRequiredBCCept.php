<?php
declare(strict_types=1);

use Codeception\Util\HttpCode;
use Flight\Service\Amadeus\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required parameter in business-case field');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/no-required-bc-fields.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
$I->seeResponseIsValidErrorResponse();

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.content-provider' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - content-provider must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['business-cases.0.0.type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - type must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication must be provided, but does not exist',
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

