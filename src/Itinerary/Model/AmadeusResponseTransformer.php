<?php
namespace Flight\Service\Amadeus\Itinerary\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Itinerary\Response\ResultResponse;
use Flight\Service\Amadeus\Itinerary\Model\Itinerary;

/**
 * Class AmadeusResponseTransformer
 *
 * @package Flight\Service\Amadeus\Remarks\Model
 */
class AmadeusResponseTransformer
{
    /**
     * maps the result to an itinerary which can be sent back to the client
     *
     * @param Result $result
     *
     * @return ResultResponse
     */
    public function mapResult(Result $result)
    {
        $remarksResponse = new ResultResponse();
//        $remarksResponse->setResult(new ArrayCollection());
        $remarksCollection = new ArrayCollection();
//        foreach ($result->response->dataElementsMaster->dataElementsIndiv as $remarks) {
//            $remarksData = $remarks->elementManagementData;
//            if (!isset($remarks->miscellaneousRemarks)) {
//                continue;
//            }
//            $remarksDataAdd = $remarks->miscellaneousRemarks;
//
//            $remarksCollection->add((new Remark())->setType($remarksDataAdd->remarks->type)->convertFromCrs($remarksDataAdd->remarks->freetext)
//                ->setManagementData(
//                    (new ManagementData())->setLineNumber($remarksData->lineNumber)->setReference(
//                        (new Reference())->setNumber($remarksData->reference->number)->setQualifier($remarksData->reference->qualifier)
//                    )->setSegmentName($remarksData->segmentName)
//                ));
//        }

        $itinerary = new \Flight\Service\Amadeus\Itinerary\Model\Itinerary();
        $itinerary->setRemarks($remarksCollection);
        var_dump($itinerary);die;
        $remarksResponse->getResult()->add($itinerary);

        return $remarksResponse;
    }

    /**
     * @see $this->mapResultRemarksRead
     *
     * @param Result $result
     * @return ResultResponse
     */
    public function mapResultRemarksAdd(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

    /**
     * @see $this->mapResultRemarksRead
     *
     * @param Result $result
     * @return ResultResponse
     */
    public function mapResultRemarksDelete(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

}
