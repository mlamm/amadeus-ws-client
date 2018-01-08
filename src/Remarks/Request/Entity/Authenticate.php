<?php

namespace Flight\Service\Amadeus\Remarks\Request\Entity;

/**
 * entity for remarksRead request
 *
 * @package Flight\Service\Amadeus\Remarks\Request\Entity
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
    public function getOfficeId()
    {
        return $this->officeId;
    }

    /**
     * setter for officeId
     *
     * @param string $officeId
     * @return Authenticate
     */
    public function setOfficeId($officeId)
    {
        $this->officeId = $officeId;
        return $this;
    }

    /**
     * getter for dutyCode
     *
     * @return string
     */
    public function getDutyCode()
    {
        return $this->dutyCode;
    }

    /**
     * setter for dutyCode
     *
     * @param string $dutyCode
     * @return Authenticate
     */
    public function setDutyCode($dutyCode)
    {
        $this->dutyCode = $dutyCode;
        return $this;
    }

    /**
     * getter for userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * setter for userId
     *
     * @param string $userId
     * @return Authenticate
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * getter for passwordData
     *
     * @return string
     */
    public function getPasswordData()
    {
        return $this->passwordData;
    }

    /**
     * setter for passwordData
     *
     * @param string $passwordData
     * @return Authenticate
     */
    public function setPasswordData($passwordData)
    {
        $this->passwordData = $passwordData;
        return $this;
    }

    /**
     * getter for passwordLength
     *
     * @return int
     */
    public function getPasswordLength()
    {
        return $this->passwordLength;
    }

    /**
     * setter for passwordLength
     *
     * @param int $passwordLength
     * @return Authenticate
     */
    public function setPasswordLength($passwordLength)
    {
        $this->passwordLength = $passwordLength;
        return $this;
    }

    /**
     * getter for organization
     *
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * setter for organization
     *
     * @param string $organizationId
     * @return Authenticate
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
        return $this;
    }


}