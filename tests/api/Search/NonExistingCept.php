<?php
/** @var \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);
$I->wantTo('see a 404 error if a resource does not exist');
$I->sendGET('/does-not-exist/');
$I->seeResponseCodeIs(404);
$I->seeHttpHeader('content-type', 'application/hal+json');
$I->seeResponseIsValidErrorResponse();

$I->canSeeResponseContainsJson(
    [
        'errors' => [
            '_' => [
                [
                    'code'    => 'ARS0404',
                    'message' => 'No route found for "GET /does-not-exist/"',
                    'status'  => 404,
                ],
            ],
        ],
    ]
);
