<?php

namespace Flight\Service\Amadeus\Itinerary\Request\Entity;

/**
 * entity for authentication request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
 */
class Session
{

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var int
     */
    private $sequenceNumber;

    /**
     * @var string
     */
    private $securityToken;

    private $transactionStatusCode = 'Start';
    /**
     * @return string
     */
    public function getSessionId() : string
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     *
     * @return Session
     */
    public function setSessionId(string $sessionId) : Session
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSequenceNumber() : int
    {
        return $this->sequenceNumber;
    }

    /**
     * @param int $sequenceNumber
     *
     * @return Session
     */
    public function setSequenceNumber(int $sequenceNumber) : Session
    {
        $this->sequenceNumber = $sequenceNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityToken() : string
    {
        return $this->securityToken;
    }

    /**
     * @param string $securityToken
     *
     * @return Session
     */
    public function setSecurityToken(string $securityToken) : Session
    {
        $this->securityToken = $securityToken;
        return $this;
    }

    /**
     * set data from a stdClass like we get on from json_decode
     * @param \stdClass $data
     *
     * @return $this
     */
    public function populate(\stdClass $data) : Session
    {
        $this->setSessionId($data->{'session-id'})
        ->setSequenceNumber($data->{'sequence-number'})
        ->setSecurityToken($data->{'security-token'});

        return $this;
    }

    /**
     * returns the properties as array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
