<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function index(): View
    {
        $refunds = RefundRequest::with(['user', 'order'])->latest()->paginate(15);
        return view('admin.refunds.index', compact('refunds'));
    }

    public function show(int $id): View
    {
        $refund = RefundRequest::with(['user', 'order.items.product'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    public function approve(Request $request, int $id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'refund_amount' => $request->refund_amount ?? $refund->order->total,
        ]);
        return redirect()->back()->with('success', 'Refund approved.');
    }

    public function reject(Request $request, int $id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note ?? 'Request rejected.',
        ]);
        return redirect()->back()->with('success', 'Refund rejected.');
    }

    public function complete(int $id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Refund completed.');
    }
}