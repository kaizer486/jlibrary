<?php

return [
    'withdrawal' => [
        'min_amount' => env('WITHDRAWAL_MIN_AMOUNT', 5000),
        'max_amount' => env('WITHDRAWAL_MAX_AMOUNT', 10000000),
    ],
    'deposit' => [
        'min_amount' => env('DEPOSIT_MIN_AMOUNT', 1000),
        'max_amount' => env('DEPOSIT_MAX_AMOUNT', 1000000),
    ],
];