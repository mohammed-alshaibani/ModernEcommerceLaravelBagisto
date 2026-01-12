<?php

return [
    'currency' => 'SAR',
    'tax_rate' => 0.15,
    'low_stock_threshold' => 5,
    'payment' => [
        'default' => 'stripe',
        'providers' => [
            'stripe' => [
                'key' => env('STRIPE_KEY'),
                'secret' => env('STRIPE_SECRET'),
            ],
            'moyasar' => [
                'key' => env('MOYASAR_KEY'),
            ],
        ],
    ],
];
