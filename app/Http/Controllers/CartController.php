<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        if (!auth()->check()) {
            return view('cart', ['items' => collect(), 'total' => 0, 'taxRate' => 0, 'shippingFee' => 0]);
        }

        $items = CartItem::with(['product.translation', 'product.primaryImage', 'variant'])
            ->where('user_id', auth()->id())
            ->get();

        $total = $items->sum('subtotal');
        $taxRate = (float) config('cart.tax_rate', 10);
        $shippingFee = $total >= 50 ? 0 : (float) config('cart.shipping_fee', 5.99);

        return view('cart', compact('items', 'total', 'taxRate', 'shippingFee'));
    }

    public function add(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variantId = $request->filled('variant_id') ? $request->variant_id : null;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            if ($variant->stock < $request->quantity) {
                return back()->with('error', __('messages.out_of_stock'));
            }
        } else {
            if ($product->stock < $request->quantity) {
                return back()->with('error', __('messages.out_of_stock'));
            }
        }

        $existing = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
                'variant_id' => $variantId,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', __('messages.add_to_cart') . ' OK');
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::where('user_id', auth()->id())
            ->findOrFail($request->item_id);

        $item->update(['quantity' => $request->quantity]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Cart updated.']);
        }

        return back()->with('success', __('messages.update') . ' OK');
    }

    public function remove(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        CartItem::where('user_id', auth()->id())
            ->findOrFail($request->item_id)
            ->delete();

        return back()->with('success', __('messages.remove') . ' OK');
    }
}
