<?php
namespace Flight\Service\Amadeus\Remarks\Model;

use Amadeus\Client\Result;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;

/**
 * Class AmadeusResponseTransformer
 * @package Flight\Service\Amadeus\Remarks\Model
 */
class AmadeusResponseTransformer
{
    public function mapResultRemarksRead(Result $result)
    {
        $remarksResponse = new ResultResponse();
        $remarksResponse->setResult(new ArrayCollection());
        $remarksCollection = new ArrayCollection();
        foreach ($result->response->dataElementsMaster->dataElementsIndiv as $remarks) {
            $remarksData = $remarks->elementManagementData;
            if (!isset($remarks->miscellaneousRemarks)) {
                continue;
            }
            $remarksDataAdd = $remarks->miscellaneousRemarks;

            $remarksCollection->add((new Remark())->setType($remarksDataAdd->remarks->type)->convertFromCrs($remarksDataAdd->remarks->freetext)
                ->setManagementData(
                    (new ManagementData())->setLineNumber($remarksData->lineNumber)->setReference(
                        (new Reference())->setNumber($remarksData->reference->number)->setQualifier($remarksData->reference->qualifier)
                    )->setSegmentName($remarksData->segmentName)
                ));
        }

        $itinerary = new Itinerary();
        $itinerary->setRemarks($remarksCollection);
        $remarksResponse->getResult()->add($itinerary);

        return $remarksResponse;
    }

    public function mapResultRemarksAdd(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

    public function mapResultRemarksDelete(Result $result)
    {
        return $this->mapResultRemarksRead($result);
    }

}
