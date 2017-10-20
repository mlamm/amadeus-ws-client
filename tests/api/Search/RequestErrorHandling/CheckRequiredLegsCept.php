<?php

declare(strict_types=1);

use Codeception\Util\HttpCode;
use AmadeusService\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required filed in the legs field');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
'/flight-search/',
file_get_contents(codecept_data_dir('requests/no-required-legs.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['legs.0.departure' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - departure must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.arrival' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - arrival must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.depart-at' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - depart-at must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.is-flexible-date' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - is-flexible-date must be provided, but does not exist',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.departure' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - departure must be provided, but does not exist',
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
