<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

/**
 *
 * model for remark data
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Itinerary
{

    /**
     * type of remark
     *
     * @var string
     */
    private $type;

    /**
     * name of the remark
     *
     * @var string
     */
    private $name;

    /**
     *  value of the remark
     *
     * @var string
     */
    private $value;

    /**
     * management data of the remark
     *
     * @var ManagementData
     */
    private $managementData;

    /**
     * getter for type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * setter for type
     *
     * @param string $type
     * @return Remark
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setter for name
     *
     * @param string $name
     * @return Remark
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getter for value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * setter for value
     *
     * @param string $value
     * @return Remark
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * getter for managementData
     *
     * @return ManagementData
     */
    public function getManagementData()
    {
        return $this->managementData;
    }

    /**
     * setter for managementData
     *
     * @param ManagementData $managementData
     * @return Remark
     */
    public function setManagementData($managementData)
    {
        $this->managementData = $managementData;
        return $this;
    }

    /**
     * @param $crsText
     * @return $this
     */
    public function convertFromCrs($crsText)
    {
        list($name, $value) = array_pad(explode('-', (string) $crsText, 2), 2, null);
        $this->setValue($value);
        $this->setName($name);

        return $this;
    }

    /**
     * returns value and name for crs useage
     *
     * @return string
     */
    public function convertToCrs()
    {
        return $this->getName() . '-' . $this->getValue();
    }
}
