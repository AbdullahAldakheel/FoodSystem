<?php

return [

        /*
        |--------------------------------------------------------------------------
        | Default System Configuration
        |--------------------------------------------------------------------------
        |
        | This option controls the default system configuration that will be used
        |
        */
    'ingredient' => [
        'threshold_percentage' => (int) env('FOOD_THRESHOLD_PERCENTAGE', 50) / 100,
    ],

];
