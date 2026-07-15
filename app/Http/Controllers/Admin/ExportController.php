<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends \App\Http\Controllers\Controller
{
    public function exportOrders(Request $request): StreamedResponse
    {
        $query = Order::with(['user', 'items'])->orderBy('created_at', 'desc');

        // Date range filtering
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="orders_' . now()->format('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Order #', 'Customer', 'Email', 'Status', 'Subtotal', 'Shipping', 'Tax', 'Discount', 'Total', 'Items', 'Date']);

            // Use cursor() to stream rows instead of loading all into memory
            foreach ($query->cursor() as $order) {
                fputcsv($file, [
                    $order->order_no,
                    $order->user?->name ?? 'N/A',
                    $order->user?->email ?? 'N/A',
                    $order->status,
                    number_format($order->subtotal, 2),
                    number_format($order->shipping_fee, 2),
                    number_format($order->tax, 2),
                    number_format($order->discount, 2),
                    number_format($order->total, 2),
                    $order->items->count(),
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
