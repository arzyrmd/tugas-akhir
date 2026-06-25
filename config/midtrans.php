<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Midtrans settings
    |
    */

    // Midtrans Server Key from Dashboard
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    // Midtrans Client Key from Dashboard (for frontend)
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    // Use production environment if true, sandbox if false
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Enable 3D Secure by default
    'is_3ds' => true,

    // Sanitize requests by default
    'is_sanitized' => true,

    // Default notification URL
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),

    // Default callback URLs
    'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL', ''),
    'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_REDIRECT_URL', ''),
    'error_redirect_url' => env('MIDTRANS_ERROR_REDIRECT_URL', ''),
];
