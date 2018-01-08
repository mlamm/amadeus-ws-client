<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Model;

/**
 * CabinClass.php
 *
 * Convert the cabin class info
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class Reference
{
    /**
     * reference qualifier
     *
     * @var string
     */
    private $qualifier;

    /**
     * reference number (tattoo number?)
     *
     * @var integer
     */
    private $number;

    /**
     * getter for qualifier
     *
     * @return string
     */
    public function getQualifier()
    {
        return $this->qualifier;
    }

    /**
     * setter for qualifier
     *
     * @param string $qualifier
     * @return Reference
     */
    public function setQualifier($qualifier)
    {
        $this->qualifier = $qualifier;
        return $this;
    }

    /**
     * getter for number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * setter for number
     *
     * @param int $number
     * @return Reference
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

}
