<?php
namespace AmadeusService\Search\Model;

use AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;

/**
 * Class AmadeusResponseParser
 * @package AmadeusService\Search\Model
 */
class AmadeusResponseParser
{
    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * AmadeusResponseParser constructor.
     */
    public function __construct()
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @param string $response
     * @return MasterPricerTravelBoardSearchReply
     */
    public function parse($response)
    {
        return $this->serializer->deserialize(
            $response,
            MasterPricerTravelBoardSearchReply::class,
            'xml'
        );
    }
}