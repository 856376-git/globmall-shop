<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $items = Wishlist::with(['product.translation', 'product.primaryImage', 'product.category.translation'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('wishlist', compact('items'));
    }

    public function toggle(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Removed from wishlist.');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            return back()->with('success', 'Added to wishlist.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $id)
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}