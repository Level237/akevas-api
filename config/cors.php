<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

     'paths' => ['api/*'],

    'allowed_methods' => ['GET, POST, PUT, DELETE, OPTIONS'],

    'allowed_origins' => ['http://localhost:5173',"https://delivery.akevas.com","https://seller.akevas.com","https://main.akevas.com","https://www.main.akevas.com","https://akevas.com","https://www.akevas.com","https://pay.notchpay.co","https://dev.akevas.com","http://localhost:4173",'http://localhost:5174'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept'],

    'exposed_headers' => ["*"],

    'max_age' => 0,
    "Access-Control-Allow-Credentials" =>true,

    'supports_credentials' => true,
       // Très important !

];