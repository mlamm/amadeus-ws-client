<?php

namespace Flight\Service\Amadeus\Remarks\Filter;

/**
 * Encoding filter for name remarks.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class EncodingFilterName extends AbstractEncodingFilter
{
    /**
     * Encode given value, remove special chars, strtoupper, replace vowel chars.
     *
     * @param string $inputValue input value to be processed
     *
     * @return string
     */
    public function encode($inputValue) : string
    {
        $inputValue = trim($inputValue);
        $inputValue = $this->mutatedVowel($inputValue);
        $inputValue = strtoupper($inputValue);

        return $inputValue;
    }
}
