<?php

namespace App\Helper;

class CalculationsHelper
{

    /**
     * @description Gram to KG converter if the weight is more than 1000 grams
     */
    static public function gramToKgConverter(int $weightInGrams): string
    {
        if ($weightInGrams >= 1000) {
            $weightInGrams = $weightInGrams / 1000;
            return $weightInGrams . ' KG';
        } else {
            return $weightInGrams . ' Grams';
        }
    }
}
