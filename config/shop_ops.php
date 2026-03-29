<?php

$isProduction = env('APP_ENV') === 'production';

return [
    'api_token' => env('SHOP_OPS_API_TOKEN', $isProduction ? null : 'local-dev-token-change-me'),
    'admin_email' => env('SHOP_OPS_ADMIN_EMAIL', $isProduction ? null : 'admin@example.local'),
    'admin_password' => env('SHOP_OPS_ADMIN_PASSWORD', $isProduction ? null : 'change-this-local-admin-password'),
];
