<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ReplyStatus;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Class Status
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ReplyStatus
 */
class Status
{
    /**
     * Advisory Type Information, Fare Server
     *
     * @Type("string")
     * @SerializedName("advisoryTypeInfo")
     * @var string
     */
    protected $advisoryTypeInfo;

    /**
     * CPU time, User Type
     *
     * @Type("string")
     * @SerializedName("notification")
     * @var string
     */
    protected $notification;

    /**
     * CPU time, User Type
     *
     * @Type("string")
     * @SerializedName("notification2")
     * @var string
     */
    protected $notification2;

    /**
     * Free text field available to the message sender for information.
     *
     * @Type("string")
     * @SerializedName("description")
     * @var string
     */
    protected $description;

    /**
     * @return string
     */
    public function getAdvisoryTypeInfo()
    {
        return $this->advisoryTypeInfo;
    }

    /**
     * @return string
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return string
     */
    public function getNotification2()
    {
        return $this->notification2;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}