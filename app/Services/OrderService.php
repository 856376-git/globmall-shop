<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CartService $cartService,
    ) {}

    public function createOrder(int $userId, array $data): Order
    {
        $items = CartItem::with(['product', 'variant'])->where('user_id', $userId)->get();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Cart is empty');
        }

        return DB::transaction(function () use ($userId, $data, $items) {
            $subtotal = $this->cartService->getSubtotal($userId);
            $shippingFee = $subtotal >= 50 ? 0 : 5.99;
            $tax = round($subtotal * 0.08, 2);
            $discount = 0;

            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $total = $subtotal + $shippingFee + $tax - $discount;
            $orderNo = 'GM' . date('YmdHis') . strtoupper(Str::random(4));

            $order = Order::create([
                'order_no' => $orderNo,
                'user_id' => $userId,
                'address_id' => $data['address_id'],
                'currency' => 'USD',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name ?? $item->product->sku,
                    'sku' => $item->variant_id ? $item->variant->sku : $item->product->sku,
                    'image' => $item->product->primaryImage?->image,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                if ($item->variant_id) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            CartItem::where('user_id', $userId)->delete();

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status, ?string $note = null): void
    {
        $oldStatus = $order->status;

        if ($oldStatus === $status) return;

        DB::transaction(function () use ($order, $oldStatus, $status, $note) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status !== $oldStatus) return;

            $updateData = ['status' => $status];

            switch ($status) {
                case 'paid': $updateData['paid_at'] = now(); break;
                case 'shipped': $updateData['shipped_at'] = now(); break;
                case 'delivered': $updateData['delivered_at'] = now(); break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    if ($oldStatus !== 'cancelled') {
                        foreach ($order->items as $item) {
                            if ($item->variant_id && $item->variant) {
                                $item->variant->increment('stock', $item->quantity);
                            } elseif ($item->product) {
                                $item->product->increment('stock', $item->quantity);
                            }
                        }
                    }
                    break;
            }

            $order->update($updateData);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $status,
                'note' => $note,
            ]);
        });
    }

    public function getUserOrders(int $userId, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrderByNo(string $orderNo, int $userId): Order
    {
        return Order::with(['items.product', 'address', 'statusLogs'])
            ->where('order_no', $orderNo)
            ->where('user_id', $userId)
            ->firstOrFail();
    }
}
