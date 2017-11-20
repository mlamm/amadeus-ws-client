<?php

use Codeception\Util\HttpCode;
use Flight\Service\Amadeus\Search\Exception\ValidationException;

/* @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see an error telling me that the request contains invalid values for filter');
$I->haveHttpHeader('content-type', 'application/json');
$I->sendPOST(
    '/flight-search/',
    file_get_contents(codecept_data_dir('requests/invalid-filter-content.json'))
);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
$I->seeResponseIsValidErrorResponse();

$I->canSeeResponseContainsJson(
    ['filter-cabin-class' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - Invalid cabin class value: X',
            'status'  => 400
        ]
    ]]
);
$I->canSeeResponseContainsJson(
    ['filter-airline' => [
        [
            'code'    => ValidationException::INTERNAL_ERROR_CODE,
            'message' => 'INVALID OR MISSING REQUEST PARAM - Invalid airline code in filter-airline: SACCCC',
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
