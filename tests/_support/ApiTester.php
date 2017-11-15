<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    public function seeResponseHasLinkToSelf($selfUrl)
    {
        $jsonPath = '$_links.self.href';
        $href = $this->grabDataFromResponseByJsonPath($jsonPath);
        \PHPUnit_Framework_Assert::assertCount(1, $href, 'Link not found at path `' . $jsonPath .'`');
        \PHPUnit_Framework_Assert::assertEquals($selfUrl, $href[0]);
    }

    public function seeResponseIsHal()
    {
        $this->seeHttpHeader('content-type', 'application/hal+json');
        $this->seeResponseIsJson();
        $this->seeResponseIsValidOn('file://' . codecept_data_dir('/schema/hal.json'));
    }

    public function seeResponseIsValidErrorResponse()
    {
        $this->seeHttpHeader('content-type', 'application/hal+json');
        $this->seeResponseIsJson();
        $this->seeResponseIsValidOn('file://' . codecept_data_dir('/schema/error-response.json'));
    }

    public function seeResponseIsValidOn($schemaUrl)
    {
        if (strpos('file://', $schemaUrl) === 0) {
            \PHPUnit_Framework_Assert::assertFileExists($schemaUrl, 'Schema not found at ' .  $schemaUrl);
        }

        $rawData = $this->getResponseContent();
        \PHPUnit_Framework_Assert::assertJson($rawData);

        $data = json_decode($this->getResponseContent());

        $validator = new JsonSchema\Validator();
        $validator->validate($data, ['$ref' => $schemaUrl]);

        $errors = array_map(function ($error) {
            return sprintf('Validation error: [%s] %s', $error['property'], $error['message']);
        }, $validator->getErrors());

        \PHPUnit_Framework_Assert::assertTrue($validator->isValid(), implode(PHP_EOL, $errors));
    }
}
