<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    public function getItems(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return CartItem::with(['product.translation', 'product.primaryImage', 'variant'])
            ->where('user_id', $userId)
            ->get();
    }

    public function getSubtotal(int $userId): float
    {
        return (float) CartItem::where('user_id', $userId)->get()->sum('subtotal');
    }

    public function getCount(int $userId): int
    {
        return (int) CartItem::where('user_id', $userId)->sum('quantity');
    }

    public function addItem(int $userId, int $productId, ?int $variantId, int $quantity): void
    {
        $product = Product::findOrFail($productId);

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            if ($variant->stock < $quantity) {
                throw new \RuntimeException(__('messages.out_of_stock'));
            }
        } elseif ($product->stock < $quantity) {
            throw new \RuntimeException(__('messages.out_of_stock'));
        }

        $existing = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
    }

    public function updateQuantity(int $userId, int $itemId, int $quantity): void
    {
        CartItem::where('user_id', $userId)
            ->findOrFail($itemId)
            ->update(['quantity' => $quantity]);
    }

    public function removeItem(int $userId, int $itemId): void
    {
        CartItem::where('user_id', $userId)
            ->findOrFail($itemId)
            ->delete();
    }

    public function clearCart(int $userId): void
    {
        CartItem::where('user_id', $userId)->delete();
    }

    public function getCartTotal(int $userId): array
    {
        $subtotal = $this->getSubtotal($userId);
        $taxRate = (float) config('cart.tax_rate', 10);
        $shippingFee = $subtotal >= 50 ? 0 : (float) config('cart.shipping_fee', 5.99);
        $total = $subtotal + $shippingFee + round($subtotal * $taxRate / 100, 2);

        return compact('subtotal', 'taxRate', 'shippingFee', 'total');
    }
}
