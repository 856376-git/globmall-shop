<?php

return [
    'tax_rate' => env('CART_TAX_RATE', 10),
    'shipping_fee' => env('CART_SHIPPING_FEE', 5.99),
    'free_shipping_threshold' => env('CART_FREE_SHIPPING_THRESHOLD', 50),
];
