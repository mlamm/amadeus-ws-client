<?php 
$I = new ApiTester($scenario);
$I->wantTo('expect the health check endpoint to return a information about the state of the application and db');
$I->sendGET('/_hc');
$I->seeResponseCodeIs(200);
$I->haveHttpHeader('content-type', 'application/hal+json');
$I->canSeeResponseContainsJson(
    [
        '_links' => [
            'search' => '/flight-search/'
        ],
        'state' => 'alive',
        'database' => [
            'ibe_cache' => 'alive'
        ]
    ]
);

