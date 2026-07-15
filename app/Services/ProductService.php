<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getHomePageData(): array
    {
        $banners = Product::with(['translation', 'primaryImage', 'category.translation'])
            ->active()->featured()->inRandomOrder()->take(5)->get();

        $flashSaleProducts = Product::with(['translation', 'primaryImage'])
            ->active()->whereNotNull('compare_price')->where('compare_price', '>', 0)
            ->inRandomOrder()->take(8)->get();

        $featuredProducts = Product::with(['translation', 'primaryImage', 'category.translation'])
            ->active()->featured()->orderBy('created_at', 'desc')->take(10)->get();

        $newProducts = Product::with(['translation', 'primaryImage', 'category.translation'])
            ->active()->new()->orderBy('created_at', 'desc')->take(10)->get();

        $bestSellers = Product::with(['translation', 'primaryImage'])
            ->active()->inRandomOrder()->take(10)->get();

        return compact('banners', 'flashSaleProducts', 'featuredProducts', 'newProducts', 'bestSellers') + ['seckillProducts' => collect([]), 'groupBuyProducts' => collect([]), 'liveProducts' => collect([])];
    }

    public function getShopProducts(array $filters = [], int $perPage = 12): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Product::with(['translation', 'primaryImage', 'category.translation', 'brand.translation'])->active();

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
        if (!empty($filters['brand'])) {
            $query->where('brand_id', $filters['brand']);
        }
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'default';
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'best_selling':
                $query->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity),0) as sold'))
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->groupBy('products.id')
                    ->orderByDesc('sold');
                break;
            default: $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getRelatedProducts(Product $product, int $take = 8): \Illuminate\Database\Eloquent\Collection
    {
        return Product::with(['translation', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take($take)
            ->get();
    }

    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::with('translation')->active()->topLevel()->orderBy('sort_order')->get();
    }

    public function getBrands(): \Illuminate\Database\Eloquent\Collection
    {
        return Brand::with('translation')->active()->orderBy('sort_order')->get();
    }
}
