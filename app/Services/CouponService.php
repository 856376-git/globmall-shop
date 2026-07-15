<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CartItem;

class CouponService
{
    public function getAvailableCoupons(): \Illuminate\Database\Eloquent\Collection
    {
        return Coupon::where('status', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->get();
    }

    public function validateAndApply(string $code, int $userId): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['error' => 'Invalid coupon code', 'code' => 404];
        }

        if (!$coupon->isValid()) {
            return ['error' => 'Coupon expired or used up', 'code' => 422];
        }

        $subtotal = (float) CartItem::where('user_id', $userId)->get()->sum('subtotal');

        if ($subtotal < $coupon->min_order_amount) {
            return ['error' => "Minimum order of \$" . number_format($coupon->min_order_amount, 2) . " required", 'code' => 422];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'success' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
            'new_total' => round($subtotal - $discount, 2),
        ];
    }
}
