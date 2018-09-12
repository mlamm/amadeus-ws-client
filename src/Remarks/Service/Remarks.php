<?php
declare(strict_types = 1);

namespace Flight\Service\Amadeus\Remarks\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Flight\Service\Amadeus\Remarks\Filter\RemarkEncoder;
use Flight\Service\Amadeus\Remarks\Model\Itinerary;
use Flight\Service\Amadeus\Remarks\Model\Remark;
use Flight\Service\Amadeus\Remarks\Model\RemarksAmadeusClient;
use Flight\Service\Amadeus\Remarks\Request;
use Flight\Service\Amadeus\Remarks\Response\ResultResponse;
use JMS\Serializer\Serializer;

/**
 * Remarks.php
 *
 * Service which remarkses the Amadeus Gds for flights.
 *
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Remarks
{
    /**
     * @var Request\Validator\Remarks
     */
    private $requestValidator;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var RemarksAmadeusClient
     */
    private $amadeusClient;

    /**
     * @var \stdClass
     */
    private $config;

    /**
     * @param Request\Validator\Remarks $requestValidator
     * @param Serializer                $serializer
     * @param RemarksAmadeusClient      $amadeusClient
     * @param \stdClass                 $config
     */
    public function __construct(
        Request\Validator\Remarks $requestValidator,
        Serializer $serializer,
        RemarksAmadeusClient $amadeusClient,
        \stdClass $config
    ) {
        $this->requestValidator = $requestValidator;
        $this->serializer       = $serializer;
        $this->amadeusClient    = $amadeusClient;
        $this->config           = $config;
    }

    /**
     * handling remarks read requests
     *
     * @param $authHeader
     * @param $recordLocator
     *
     * @return mixed|string
     * @throws \Flight\Service\Amadeus\Remarks\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException
     */
    public function remarksRead($authHeader, $recordLocator)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateRecordlocator($recordLocator);

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        $response = $this->amadeusClient->remarksRead(
            (new Request\Entity\RemarksRead())->setRecordLocator($recordLocator),
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * handling remarks add request
     *
     * @param $authHeader
     * @param $body
     *
     * @return mixed|string
     * @return mixed|string
     * @throws \Flight\Service\Amadeus\Remarks\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException
     */
    public function remarksAdd($authHeader, $body)
    {
        $authHeader = \GuzzleHttp\json_decode($authHeader);
        $body       = \GuzzleHttp\json_decode($body);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateRecordlocator($body->recordLocator);

        $remarks = new ArrayCollection();
        foreach ($body->remarks as $remarkString) {
            // first item has the remarkName so we explode it out of the array to let the remarkValue rest in pieces
            $remark     = explode('-', $remarkString);
            $remarkName = $remark[0];
            unset($remark[0]);
            $remarkValue = implode('-', $remark);
            $remarks->add(
                (new Remark())
                    ->setName($remarkName)
                    ->setValue((new RemarkEncoder($remarkName, $remarkValue))->get())
            );
        }

        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});
        $response     = $this->amadeusClient->remarksAdd(
            (new Request\Entity\RemarksAdd())
                ->setRecordLocator($body->recordLocator)->setRemarks($remarks),
            $authenticate
        );

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * handling remarks delete requests
     *
     * @param $authHeader
     * @param $body
     *
     * @return mixed|string
     * @throws \Flight\Service\Amadeus\Remarks\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException
     */
    public function remarksDelete($authHeader, $body)
    {
        // json data
        $authHeader = \GuzzleHttp\json_decode($authHeader);
        $body       = \GuzzleHttp\json_decode($body);

        // validate
        $this->requestValidator->validateAuthentication($authHeader);
        $this->requestValidator->validateRecordlocator($body->recordLocator);

        // authenticate
        $authenticate = (new Request\Entity\Authenticate())
            ->setDutyCode($authHeader->{'duty-code'})
            ->setOfficeId($authHeader->{'office-id'})
            ->setOrganizationId($authHeader->{'organization'})
            ->setPasswordData($authHeader->{'password-data'})
            ->setPasswordLength($authHeader->{'password-length'})
            ->setUserId($authHeader->{'user-id'});

        // get remarks for line number
        $response = $this->amadeusClient->remarksRead(
            (new Request\Entity\RemarksRead())->setRecordLocator($body->recordLocator),
            $authenticate
        );

        // filter remarks tp delete
        /** @var Itinerary $remarksReadCollection */
        $remarksReadCollection   = $response->getResult()->get(0);
        $remarksDeleteCollection = new ArrayCollection();

        /** @var Remark $remark */
        foreach ($remarksReadCollection->getRemarks() as $remark) {
            foreach ($body->remarks as $remarkString) {
                // first item has the remarkName so we explode it out of the array to let the remarkValue rest in pieces
                $remarkData = explode('-', $remarkString);
                if ($remarkData[0] == $remark->getName()) {
                    $remarksDeleteCollection->add($remark);
                }
            }
        }
        // be clean, remove garbage
        unset($remarksReadCollection);

        if (!$remarksDeleteCollection->isEmpty()) {
            $response = $this->amadeusClient->remarksDelete(
                (new Request\Entity\RemarksDelete())
                    ->setRecordLocator($body->recordLocator)->setRemarks($remarksDeleteCollection),
                $authenticate
            );
        } else {
            $response = (new ResultResponse())->setResult(new ArrayCollection());
        }

        return $this->serializer->serialize($response, 'json');
    }

    /**
     * handling remarks modify request by using delete and add
     *
     * @param $authHeader
     * @param $body
     *
     * @return mixed|string
     * @throws \Flight\Service\Amadeus\Remarks\Exception\AmadeusRequestException
     * @throws \Flight\Service\Amadeus\Remarks\Exception\InvalidRequestParameterException
     */
    public function remarksModify($authHeader, $body)
    {
        $this->remarksDelete($authHeader, $body);

        return $this->remarksAdd($authHeader, $body);
    }
}
