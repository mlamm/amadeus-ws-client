<?php

use Codeception\Util\HttpCode;
use AmadeusService\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request contains wrong value types');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('required-wrong-type.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['agent' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - agent must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['adults' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - adults must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['children' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - children must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['infants' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - infants must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.depart-at' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - depart-at must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.is-flexible-date' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - is-flexible-date must be either true or false',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.content-provider' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - content-provider must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - type must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.fare-type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - fare-type must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.fare-type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - fare-type must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-one-way-combination' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-one-way-combination must be either true or false',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-overnight' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-overnight must be either true or false',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-area-search' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-area-search must be either true or false',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-benchmark' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-benchmark must be either true or false',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.result-limit' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.result-limit must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.office-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.office-id must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.user-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.user-id must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-data' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-data must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-length' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-length must be an integer',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.duty-code' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.duty-code must be a string',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.organization-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.organization-id must be a string',
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
