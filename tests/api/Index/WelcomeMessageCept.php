<?php
/** @var \Codeception\Scenario $scenario */
$i = new ApiTester($scenario);
$i->wantTo('see a welcome message');
$i->sendGET('/');
$i->seeResponseIsJson();
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseContainsJson(['name' => 'amadeus-service']);
