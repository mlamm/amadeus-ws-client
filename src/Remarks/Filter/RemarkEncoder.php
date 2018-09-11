<?php

namespace Flight\Service\Amadeus\Remarks\Filter;

/**
 * Remark encoding class to encode remarks based on their name/type.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class RemarkEncoder
{
    /**
     * Key/Name of Remark (e.g. IBEBNAME).
     *
     * @var string
     */
    private $remarkName;

    /**
     * Value of remark.
     *
     * @var string
     */
    private $remarkValue;

    /**
     * RemarkEncoder constructor.
     *
     * @param string $remarkName  $remarkName name of remark (e.g. IBEBNAME)
     * @param string $remarkValue $remarkValue value of remark
     */
    public function __construct(string $remarkName, string $remarkValue)
    {
        $this->remarkName  = $remarkName;
        $this->remarkValue = $remarkValue;
    }

    /**
     * Encode and return remark.
     *
     * @return string
     */
    public function get() : string
    {
        return $this->encodeRemarkValue($this->remarkName, $this->remarkValue);
    }

    /**
     * Encode given remark value by applying remark-key-specific filter to it.
     *
     * @param string $remarkName  name of remark (e.g. IBEBNAME)
     * @param string $remarkValue value of remark
     *
     * @return string
     */
    private function encodeRemarkValue(string $remarkName, string $remarkValue) : string
    {
        switch ($remarkName) {
            case 'IBEBNAME':
                return (new EncodingFilterName())->encode($remarkValue);
            case 'IBEBEMAIL':
            case 'IBEPAXEMAIL':
                return (new EncodingFilterMail())->encode($remarkValue);
            case 'IBEBCOMPANY':
            case 'IBEBSTREET':
            case 'IBEBZIP':
            case 'IBEBCITY':
            case 'IBEBCOUNTRY':
                return (new EncodingFilterCommon())->encode($remarkValue);

            // unfiltered
            default:
                return $remarkValue;
        }
    }
}