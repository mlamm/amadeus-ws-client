<?php

use Codeception\Util\HttpCode;
use AmadeusService\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request does not contain a required parameter');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/empty-required-fields.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

$I->canSeeResponseContainsJson(
    ['agent' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - agent must not be empty',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['adults' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - adults must not be empty',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['children' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - children must not be empty',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['infants' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - infants must not be empty',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['legs.0.departure' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - departure must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.arrival' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - arrival must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.depart-at' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - depart-at must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.is-flexible-date' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - is-flexible-date must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['legs.0.departure' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - departure must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.content-provider' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - content-provider must not be empty',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['business-cases.0.0.type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - type must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.fare-type' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - fare-type must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-one-way-combination' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-one-way-combination must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-overnight' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-overnight must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-area-search' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-area-search must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.is-benchmark' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.is-benchmark must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.options.result-limit' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - options.result-limit must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.office-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.office-id must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.user-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.user-id must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-data' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-data must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.password-length' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.password-length must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.duty-code' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.duty-code must not be empty',
            'status'  => 400
        ]
    ]]
);

$I->canSeeResponseContainsJson(
    ['business-cases.0.0.authentication.organization-id' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - authentication.organization-id must not be empty',
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
