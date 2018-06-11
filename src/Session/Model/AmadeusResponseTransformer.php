<?php

namespace Flight\Service\Amadeus\Session\Model;

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
}