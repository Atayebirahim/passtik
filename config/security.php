<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers for the application
    |
    */

    'headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Define Content Security Policy for the application
    |
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', false),
        'report_only' => env('CSP_REPORT_ONLY', true),
        'directives' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
            'style-src' => ["'self'", "'unsafe-inline'"],
            'img-src' => ["'self'", 'data:', 'https:'],
            'font-src' => ["'self'", 'data:'],
            'connect-src' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various endpoints
    |
    */

    'rate_limits' => [
        'api' => env('RATE_LIMIT_API', 60),
        'login' => env('RATE_LIMIT_LOGIN', 5),
        'register' => env('RATE_LIMIT_REGISTER', 5),
        'password_reset' => env('RATE_LIMIT_PASSWORD_RESET', 3),
        'voucher_redeem' => env('RATE_LIMIT_VOUCHER_REDEEM', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Whitelist IPs for admin access (optional)
    |
    */

    'admin_ip_whitelist' => array_filter(explode(',', env('ADMIN_IP_WHITELIST', ''))),

    /*
    |--------------------------------------------------------------------------
    | Password Requirements
    |--------------------------------------------------------------------------
    |
    | Configure password strength requirements
    |
    */

    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Additional session security settings
    |
    */

    'session' => [
        'regenerate_on_login' => true,
        'invalidate_on_logout' => true,
        'timeout_warning' => 5, // minutes before timeout
    ],
];
