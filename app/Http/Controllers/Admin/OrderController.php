<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_no')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->order_no);
            $query->where('order_no', 'like', "%{$search}%");
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'address', 'statusLogs']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $status = $request->status;

        if ($oldStatus === $status) {
            return back()->with('info', 'Order status unchanged.');
        }

        DB::transaction(function () use ($order, $oldStatus, $status, $request) {
            // Lock the order to prevent race conditions
            $order = Order::lockForUpdate()->findOrFail($order->id);

            // Double-check after lock
            if ($order->status !== $oldStatus) {
                return;
            }

            $updateData = ['status' => $status];

            switch ($status) {
                case 'paid':
                    $updateData['paid_at'] = now();
                    break;
                case 'shipped':
                    $updateData['shipped_at'] = now();
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;
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
                'note' => $request->note,
            ]);
        });

        return back()->with('success', 'Order status updated.');
    }
}
