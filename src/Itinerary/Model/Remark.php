<?php

namespace Flight\Service\Amadeus\Itinerary\Model;

use Flight\Service\Amadeus\Itinerary\Model\Itinerary\AbstractModel;

/**
 *
 * model for remark data
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Remark extends AbstractModel
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
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $this->type= $data->type ?? 'RM';
        list($this->name, $this->value) = array_pad(explode('-', (string) $data->freetext, 2), 2, null);
    }

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
}
