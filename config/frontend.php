<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default frontend values
    |
    */

    'url' => env('FRONTEND_URL', 'http://localhost:9000'),
    'email_verification' => env('FRONEND_EMAIL_VERIFICATION', '/verify?signedUrl=')

];
