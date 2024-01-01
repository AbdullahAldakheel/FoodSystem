<?php

namespace App\Helper;

class CalculationsHelper
{

    /**
     * @description Gram to KG converter if the weight is more than 1000 grams
     */
    static public function gramToKgConverter(int $weight_in_grams): string
    {
        if ($weight_in_grams >= 1000) {
            $weight_in_grams = $weight_in_grams / 1000;
            return $weight_in_grams . ' KG';
        } else {
            return $weight_in_grams . ' Grams';
        }
    }
}
