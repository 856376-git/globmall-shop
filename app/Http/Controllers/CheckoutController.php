<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $items = CartItem::with(['product.translation', 'product.primaryImage', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('error', __('messages.empty_cart'));
        }

        $addresses = Address::where('user_id', auth()->id())->get();
        $subtotal = $items->sum('subtotal');
        $shippingFee = $this->calculateShipping($subtotal);
        $tax = $this->calculateTax($subtotal);
        $total = $subtotal + $shippingFee + $tax;

        return view('checkout', compact('items', 'addresses', 'subtotal', 'shippingFee', 'tax', 'total'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'coupon_code' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ]);

        $items = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart');
        }

        return DB::transaction(function () use ($request, $items) {
            $subtotal = $items->sum('subtotal');
            $shippingFee = $this->calculateShipping($subtotal);
            $tax = $this->calculateTax($subtotal);
            $discount = 0;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $total = $subtotal + $shippingFee + $tax - $discount;
            $orderNo = 'GM' . date('Ymd') . strtoupper(Str::random(6));

            $order = Order::create([
                'order_no'      => $orderNo,
                'user_id'       => auth()->id(),
                'address_id'    => $request->address_id,
                'currency'      => 'USD',
                'subtotal'      => $subtotal,
                'shipping_fee'  => $shippingFee,
                'tax'           => $tax,
                'discount'      => $discount,
                'total'         => $total,
                'status'        => 'pending',
                'note'          => $request->note,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'variant_id'   => $item->variant_id,
                    'product_name' => $item->product->name ?? $item->product->sku,
                    'sku'          => $item->variant_id ? $item->variant->sku : $item->product->sku,
                    'image'        => $item->product->primaryImage?->image,
                    'price'        => $item->unit_price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                ]);

                if ($item->variant_id) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            CartItem::where('user_id', auth()->id())->delete();

            // Send order confirmation email
            Mail::to($order->user)->send(new OrderConfirmation($order));

            return redirect()->route('checkout.success', $orderNo)
                ->with('success', __('messages.order_placed'));
        });
    }

    public function success(string $orderNo): View
    {
        $order = Order::with(['items.product', 'address'])
            ->where('order_no', $orderNo)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('checkout-success', compact('order'));
    }

    private function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 50 ? 0 : 5.99;
    }

    private function calculateTax(float $subtotal): float
    {
        return round($subtotal * 0.08, 2);
    }
}