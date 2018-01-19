<?php
namespace Flight\Service\Amadeus\Remarks\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Application\Response\HalResponse;

/**
 * Class RemarksResultResponse
 * @package Flight\Service\Amadeus\Remarks\Response
 */
class ResultResponse extends HalResponse
{
    /**
     * @var ArrayCollection
     */
    protected $result;

    /**
     * @return ArrayCollection
     */
    public function getResult(): ArrayCollection
    {
        return $this->result;
    }

    /**
     * @param ArrayCollection $result
     * @return ResultResponse
     */
    public function setResult(ArrayCollection $result): ResultResponse
    {
        $this->result = $result;
        return $this;
    }

}
