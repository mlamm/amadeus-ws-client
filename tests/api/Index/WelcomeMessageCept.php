<?php
/** @var \Codeception\Scenario $scenario */
$i = new ApiTester($scenario);
$i->wantTo('want to see a welcome message');
$i->sendGET('/');
$i->seeResponseIsJson();
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseContainsJson(['message' => 'Welcome to the amadeus search service']);
