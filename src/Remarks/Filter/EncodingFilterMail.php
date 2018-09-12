<?php

namespace Flight\Service\Amadeus\Remarks\Filter;

/**
 * Encoding filter for mail remarks.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
class EncodingFilterMail extends AbstractEncodingFilter
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

        $inputValue = str_replace(['@', '_'], ['-AT-', '-CHR095-'], $inputValue);
        $inputValue = preg_replace('/[^A-Z0-9\!\#\$\%\&\*\+\-\/\=\?\^\_\`\.\{\|\}\~\@\']/i', '', $inputValue);
        $inputValue = strtoupper($inputValue);

        return $inputValue;
    }
}
