<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(string $orderNo): RedirectResponse
    {
        $order = Order::with('items')->where('order_no', $orderNo)->where('user_id', auth()->id())->firstOrFail();

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $item->product_name],
                    'unit_amount' => intval($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['order_no' => $order->order_no]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout') . '?status=cancelled',
                'metadata' => ['order_no' => $order->order_no],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe error: ' . $e->getMessage());
            return back()->with('error', 'Payment initialization failed.');
        }
    }

    public function success(Request $request, string $orderNo): RedirectResponse
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('orders')->with('error', 'Invalid payment session.');
        }

        try {
            $session = Session::retrieve($sessionId);
            $order = Order::where('order_no', $orderNo)->where('user_id', auth()->id())->firstOrFail();

            if ($session->payment_status === 'paid') {
                $order->update([
                    'status' => 'paid',
                    'payment_method' => 'stripe',
                    'payment_id' => $session->payment_intent,
                    'paid_at' => now(),
                ]);
                return redirect()->route('orders')->with('success', 'Payment successful! Order confirmed.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe success error: ' . $e->getMessage());
        }

        return redirect()->route('orders')->with('error', 'Payment verification failed.');
    }
}