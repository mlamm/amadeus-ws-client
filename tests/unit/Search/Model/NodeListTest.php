<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Tests\Search\Model;

use Flight\Service\Amadeus\Search\Model\NodeList;

/**
 * NodeListTest.php
 *
 * @covers Flight\Service\Amadeus\Search\Model\NodeList
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class NodeListTest extends \Codeception\Test\Unit
{
    /**
     * Verify that it converts the incoming mixed data to a list of values
     *
     * @dataProvider provideTestCases
     */
    public function testItConvertsIfNecessary($input, array $expectedArray)
    {
        $object = new NodeList($input);
        $this->assertEquals($expectedArray, $object->toArray());
    }

    /**
     * Provide test cases for testItConvertsIfNecessary
     *
     * @return array
     */
    public function provideTestCases()
    {
        return [
            'wrap int'     => [1,                   [1]],
            'wrap string'  => ['a',                 ['a']],
            'wrap object'  => [(object) ['a' => 1], [(object) ['a' => 1]]],
            'convert null' => [null,                []],

            'pass empty array'     => [[],         []],
            'pass array of int'    => [[1],        [1]],
            'pass array of string' => [['a'],      ['a']],
            'pass assoc keys'      => [['a' => 1], ['a' => 1]],
        ];
    }
}
