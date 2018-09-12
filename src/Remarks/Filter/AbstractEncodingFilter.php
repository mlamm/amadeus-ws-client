<?php

namespace Flight\Service\Amadeus\Remarks\Filter;

/**
 * Shared functionality between filters.
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
abstract class AbstractEncodingFilter
{
    /**
     * Transfers ä, ö, ü to ae, oe, ue.
     *
     * @param string $inputValue input value to be filtered
     *
     * @return string
     */
    protected function mutatedVowel(string $inputValue) : string
    {
        $vowels = [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ß' => 'ss',
        ];

        array_walk(
            $vowels,
            function ($replace, $search) use (&$inputValue) {
                $inputValue = str_replace($search, $replace, $inputValue);
            }
        );

        return $inputValue;
    }
}