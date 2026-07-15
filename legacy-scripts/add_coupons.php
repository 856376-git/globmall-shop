<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coupon;

$coupons = [
    ['code' => 'SUMMER25', 'type' => 'percent', 'value' => 25, 'min_order_amount' => 100, 'usage_limit' => 50, 'expires_at' => '2026-08-31'],
    ['code' => 'FLASH50', 'type' => 'fixed', 'value' => 50, 'min_order_amount' => 200, 'usage_limit' => 20, 'expires_at' => '2026-07-31'],
    ['code' => 'NEWUSER', 'type' => 'percent', 'value' => 15, 'min_order_amount' => 0, 'usage_limit' => 500, 'expires_at' => '2026-12-31'],
];

foreach ($coupons as $c) {
    Coupon::updateOrCreate(
        ['code' => $c['code']],
        array_merge($c, ['status' => 1, 'starts_at' => now()])
    );
    echo "Created: {$c['code']}\n";
}
echo "Done\n";
