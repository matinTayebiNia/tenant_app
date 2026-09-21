<?php

return [
    'name' => 'Core',
    'per_page' => env('PER_PAGE', 9),
    'limit_per_page' => env('LIMIT_PER_PAGE', 40),
    'expiration' => 2880,
    'min_age' => env('MIN_AGE', 10),
    'max_age' => env('MAX_AGE', 100),
    'sort_field' => explode(',', env('SORT_FIELD', 'id')),
    'pdf_limit' => env('APP_DEBUG', 1000000),
    'servicer'=>[
        'mobile'=>env('SERVICER_MOBILE', '09196000886'),
    ],
    'admin'=>[
        'mobile'=>env('ADMIN_MOBILE', '09196000886'),
    ]
];
