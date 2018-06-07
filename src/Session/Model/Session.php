<?php

namespace Flight\Service\Amadeus\Session\Model;

/**
 * session entity
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Session
{
    private $sessionId;

    private $sequenceNumber;

    private $securityToken;

    /**
     * getter for $this->SessionId
     *
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * setter for $this->SessionId
     *
     * @param mixed $sessionId
     * @return $this
     */
    public function setSessionId($sessionId): Session
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * getter for $this->ConversationId
     *
     * @return mixed
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * setter for $this->ConversationId
     *
     * @param mixed $sequenceNumber
     * @return $this
     */
    public function setSequenceNumber($sequenceNumber): Session
    {
        $this->sequenceNumber = $sequenceNumber;
        return $this;
    }

    /**
     * getter for $this->SecurityToken
     *
     * @return mixed
     */
    public function getSecurityToken()
    {
        return $this->securityToken;
    }

    /**
     * setter for $this->SecurityToken
     *
     * @param mixed $securityToken
     * @return $this
     */
    public function setSecurityToken($securityToken): Session
    {
        $this->securityToken = $securityToken;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}