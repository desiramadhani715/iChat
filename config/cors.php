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

        /*
     * A cors profile determines which origins, methods, headers are allowed for
     * a given requests. The `DefaultProfile` reads its configuration from this
     * config file.
     *
     * You can easily create your own cors profile.
     * More info: https://github.com/spatie/laravel-cors/#creating-your-own-cors-profile
     */
    'cors_profile' => Spatie\Cors\CorsProfile\DefaultProfile::class,

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => [ 'POST',
    'GET',
    'OPTIONS',
    'PUT',
    'PATCH',
    'DELETE', 'Access-Control-Allow-Methods'],

    'allowed_origins' => ['http://localhost:8080', 'https://api-booking.makutapro.id','https://booking.makutapro.id', 'https://booking-dev.makutapro.id'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['X-Custom-Header', 'Upgrade-Insecure-Requests', '*'],
    // 'allowed_headers' => [ 'Content-Type',
    // 'X-Auth-Token',
    // 'Origin',
    // 'Authorization', ],

    'exposed_headers' => [ 
    'Cache-Control',
    'Content-Language',
    'Content-Type',
    'Expires',
    'Last-Modified',
    'Pragma',],

    'max_age' => 60 * 60 * 24,

    'supports_credentials' => true,
    
    'forbidden_response' => [
        'message' => 'Forbidden (cors).',
        'status' => 403,
    ],

];
