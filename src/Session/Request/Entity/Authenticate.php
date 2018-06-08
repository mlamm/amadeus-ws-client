<?php

namespace Flight\Service\Amadeus\Session\Request\Entity;

/**
 * entity for authentication request
 *
 * @author      Alexej Bornemann <alexej.bornemann@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class Authenticate
{
    /**
     * @var string office id for crs
     */
    private $officeId;

    /**
     * @var string amadeus duty code
     */
    private $dutyCode;

    /**
     * @var string user id
     */
    private $userId;

    /**
     * @var string passwordData (password)
     */
    private $passwordData;

    /**
     * @var integer password length
     */
    private $passwordLength;

    /**
     * @var string organisation the user belongs to
     */
    private $organizationId;

    /**
     * getter for officeId
     *
     * @return string
     */
    public function getOfficeId(): ?string
    {
        return $this->officeId;
    }

    /**
     * setter for officeId
     *
     * @param string $officeId
     * @return Authenticate
     */
    public function setOfficeId($officeId): Authenticate
    {
        $this->officeId = $officeId;
        return $this;
    }

    /**
     * getter for dutyCode
     *
     * @return string
     */
    public function getDutyCode(): ?string
    {
        return $this->dutyCode;
    }

    /**
     * setter for dutyCode
     *
     * @param string $dutyCode
     * @return Authenticate
     */
    public function setDutyCode($dutyCode): Authenticate
    {
        $this->dutyCode = $dutyCode;
        return $this;
    }

    /**
     * getter for userId
     *
     * @return string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * setter for userId
     *
     * @param string $userId
     * @return Authenticate
     */
    public function setUserId($userId): Authenticate
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * getter for passwordData
     *
     * @return string
     */
    public function getPasswordData(): ?string
    {
        return $this->passwordData;
    }

    /**
     * setter for passwordData
     *
     * @param string $passwordData
     * @return Authenticate
     */
    public function setPasswordData($passwordData): Authenticate
    {
        $this->passwordData = $passwordData;
        return $this;
    }

    /**
     * getter for passwordLength
     *
     * @return int
     */
    public function getPasswordLength(): ?int
    {
        return $this->passwordLength;
    }

    /**
     * setter for passwordLength
     *
     * @param int $passwordLength
     * @return Authenticate
     */
    public function setPasswordLength($passwordLength): Authenticate
    {
        $this->passwordLength = (int)$passwordLength;
        return $this;
    }

    /**
     * getter for organization
     *
     * @return string
     */
    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    /**
     * setter for organization
     *
     * @param string $organizationId
     * @return Authenticate
     */
    public function setOrganizationId($organizationId): Authenticate
    {
        $this->organizationId = $organizationId;
        return $this;
    }
}