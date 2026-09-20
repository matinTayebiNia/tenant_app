<?php

return [
    'name' => 'Core',
    'per_page' => env('PER_PAGE', 9),
    'limit_per_page' => env('LIMIT_PER_PAGE', 40),
    'kave_base_url' => env('KAVE_BASE_URL', 'https://api.kavenegar.com/v1/{key}/sms/send.json'),
    'kave_api_key' => env('KAVEH_API_KEY'),
    'expiration' => 2880,
    'min_age' => env('MIN_AGE', 10),
    'max_age' => env('MAX_AGE', 100),
    'sort_field' => explode(',', env('SORT_FIELD', 'id,created_at')),
    'pdf_limit' => env('APP_DEBUG', 1000000),
    'servicer'=>[
        'mobile'=>env('SERVICER_MOBILE', '09196000886'),
    ],
    'admin'=>[
        'mobile'=>env('ADMIN_MOBILE', '09196000886'),
    ]
];
