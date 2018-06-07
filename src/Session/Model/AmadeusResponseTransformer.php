<?php

namespace Flight\Service\Amadeus\Session\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Flight\Library\SearchRequest\ResponseMapping\Entity\SearchResponse\Result;
use Flight\Service\Amadeus\Session\Response\ResultResponse;

/**
 * AmadeusResponseTransformer
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusResponseTransformer
{
    /**
     * @param string $response
     * @return Session
     */
    public function mapResultSessionCreate(String $response): Session
    {
        $xml = new \SimpleXMLElement($response);
        $result = new Session();
        $result->setSecurityToken((string)$xml->xpath('//awsse:SecurityToken')[0]);
        $result->setSessionId((string)$xml->xpath('//awsse:SessionId')[0]);
        $result->setSequenceNumber((string)$xml->xpath('//awsse:SequenceNumber')[0]);
        return $result;
    }

    /**
     * @param String $response
     * @return Session
     */
    public function mapResultSessionCreateFromHeader(String $response): Session
    {
        $xml = new \SimpleXMLElement($response);
        $result = new Session();
        $result->setSecurityToken((string)$xml->xpath('//awsse:SecurityToken')[0]);
        $result->setSessionId((string)$xml->xpath('//awsse:SessionId')[0]);
        $result->setSequenceNumber((string)$xml->xpath('//awsse:SequenceNumber')[0]);
        return $result;
    }

    public function mapSessionIgnore(\Amadeus\Client\Result $result): ResultResponse
    {
        $SessionResponse = new ResultResponse();
        $SessionResponse->setResult(new ArrayCollection());

        if (!empty($result->response->clearInformation->actionRequest)) {
            $SessionResponse->getResult()->add('session succesfully ignored.');
            return $SessionResponse;
        }

        $SessionResponse->getResult()->add('error while ignored.');

        return $SessionResponse;    }

    public function mapSessionTerminate(\Amadeus\Client\Result $result): ResultResponse
    {
        $SessionResponse = new ResultResponse();
        $SessionResponse->setResult(new ArrayCollection());

        if (!empty($result->response->processStatus->statusCode)) {
            $SessionResponse->getResult()->add('session succesfully terminated(sign out).');
            return $SessionResponse;
        }

        $SessionResponse->getResult()->add('error while termination(sign out).');

        return $SessionResponse;
    }
}