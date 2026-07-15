<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'fixed',
            'value' => 10.00,
            'min_order_amount' => 50.00,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
            'status' => 1,
        ]);

        Coupon::create([
            'code' => 'SUMMER20',
            'type' => 'percent',
            'value' => 20.00,
            'min_order_amount' => 100.00,
            'usage_limit' => 50,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(2),
            'status' => 1,
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'type' => 'fixed',
            'value' => 5.99,
            'min_order_amount' => 0,
            'usage_limit' => null,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => null,
            'status' => 1,
        ]);
    }
}