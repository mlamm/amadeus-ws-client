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
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

    /**
     * Validate the json string against the schema
     *
     * @param $data
     * @param $schema
     */
    public function canSeeJsonStringIsValidOnSchema($data, $schema)
    {
        $schemaRef = (object)['$ref' => 'file://' . realpath($schema)];

        $validator = new \JsonSchema\Validator();
        $decodedResponse = json_decode($data);
        $validator->validate($decodedResponse, $schemaRef);

        $message = '';
        $isValid = $validator->isValid();
        if (!$isValid) {
            $message = 'JSON does not validate. Violations:' . PHP_EOL;
            foreach ($validator->getErrors() as $error) {
                $message .= $error['property'] . ' ' . $error['message'] . PHP_EOL;
            }
        }

        $this->assertTrue($isValid, $message);
    }
}
