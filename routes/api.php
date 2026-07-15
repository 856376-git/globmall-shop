<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\CartApiController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('search/suggestions', [ProductController::class, 'searchSuggestions']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', fn () => auth()->user());
        Route::get('cart', [CartApiController::class, 'index']);
        Route::post('cart/add', [CartApiController::class, 'add']);
        Route::delete('cart/{id}', [CartApiController::class, 'remove']);
        Route::get('orders', fn () => response()->json([]));
        Route::post('orders', fn () => response()->json(['message' => 'created'], 201));
    });
});
