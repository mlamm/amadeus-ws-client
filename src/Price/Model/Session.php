<?php

namespace Flight\Service\Amadeus\Price\Model;

/**
 * Session entity
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
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

    /**
     * getter for $this->sessionId
     *
     * @return string
     */
    public function getSessionId() : ?string
    {
        return $this->sessionId;
    }

    /**
     * setter for $this->sessionId
     *
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
     * getter for $this->SequenceNumber
     *
     * @return int
     */
    public function getSequenceNumber() : ?int
    {
        return $this->sequenceNumber;
    }

    /**
     * setter for $this->SequenceNumber
     *
     * @param int $sequenceNumber
     *
     * @return Session
     */
    public function setSequenceNumber($sequenceNumber) : Session
    {
        $this->sequenceNumber = (int) $sequenceNumber;
        return $this;
    }

    /**
     * getter for $this->SecurityToken
     *
     * @return string
     */
    public function getSecurityToken() : ?string
    {
        return $this->securityToken;
    }

    /**
     * setter for $this->SecurityToken
     *
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
     * returns the properties as array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}