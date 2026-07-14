<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pakasir Payment Gateway
    |--------------------------------------------------------------------------
    */

    'pakasir_secret_key'    => env('PAKASIR_SECRET_KEY'),
    'pakasir_public_key'    => env('PAKASIR_PUBLIC_KEY'),
    'pakasir_is_production' => env('PAKASIR_IS_PRODUCTION', false),

];
