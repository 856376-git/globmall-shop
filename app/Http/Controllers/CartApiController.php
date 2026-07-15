<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function count(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }
        $count = CartItem::where('user_id', auth()->id())->sum('quantity');
        return response()->json(['count' => $count]);
    }
}