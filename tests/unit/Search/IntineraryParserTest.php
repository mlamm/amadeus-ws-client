<?php
namespace Search;

use AmadeusService\Search\Entity\SearchResponse;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class IntineraryParserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Serializer
     */
    protected $serializer;

    protected function _before()
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function testDeserialize()
    {
        $content = file_get_contents(codecept_data_dir() . '/result.json');
        $result = $this->serializer->deserialize(
            $content,
            SearchResponse::class,
            'json'
        );

        die(print_r($this->serializer->serialize($result, 'xml'), true));
    }
}