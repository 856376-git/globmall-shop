<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ProductCacheService
{
    private const CACHE_TTL = 3600; // 1 hour

    public static function getFeaturedProducts()
    {
        return Cache::remember('featured_products', self::CACHE_TTL, function () {
            return \App\Models\Product::with(['translation', 'primaryImage', 'category.translation'])
                ->active()
                ->featured()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });
    }

    public static function getNewProducts()
    {
        return Cache::remember('new_products', self::CACHE_TTL, function () {
            return \App\Models\Product::with(['translation', 'primaryImage', 'category.translation'])
                ->active()
                ->new()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });
    }

    public static function getCategories()
    {
        return Cache::remember('categories', self::CACHE_TTL, function () {
            return \App\Models\Category::with(['translation', 'children.translation'])
                ->active()
                ->topLevel()
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function clearProductCache()
    {
        Cache::forget('featured_products');
        Cache::forget('new_products');
    }

    public static function clearCategoryCache()
    {
        Cache::forget('categories');
    }
}