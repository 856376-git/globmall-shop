<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function model(): string
    {
        return Product::class;
    }

    public function getHomePageData(): array
    {
        return [
            'banners' => $this->model->with(['translation', 'primaryImage'])->active()->featured()->inRandomOrder()->take(5)->get(),
           'flashSaleProducts' => $this->model->with(['translation', 'primaryImage'])->active()->whereNotNull('compare_price')->where('compare_price', '>', 0)->inRandomOrder()->take(8)->get(),
            'featuredProducts' => $this->model->with(['translation', 'primaryImage', 'category.translation'])->active()->featured()->orderBy('created_at', 'desc')->take(10)->get(),
            'newProducts' => $this->model->with(['translation', 'primaryImage', 'category.translation'])->active()->orderBy('created_at', 'desc')->take(10)->get(),
            'bestSellers' => $this->model->with(['translation', 'primaryImage'])->active()->inRandomOrder()->take(10)->get(),
           'seckillProducts' => collect([]),
           'groupBuyProducts' => collect([]),
           'liveProducts' => collect([]),
        ];
    }

    public function getShopProducts(array $filters = [], int $perPage = 12)
    {
        $query = $this->model->with(['translation', 'primaryImage', 'category.translation', 'brand.translation'])->active();

        if (!empty($filters['category_id'])) $query->where('category_id', $filters['category_id']);
        if (!empty($filters['brand_id'])) $query->where('brand_id', $filters['brand_id']);
        if (!empty($filters['min_price'])) $query->where('price', '>=', $filters['min_price']);
        if (!empty($filters['max_price'])) $query->where('price', '<=', $filters['max_price']);
        if (!empty($filters['search'])) $query->whereHas('translation', fn($q) => $q->where('name', 'like', '%'.$filters['search'].'%'));

        match ($filters['sort'] ?? 'default') {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
        };

        return $query->paginate($perPage);
    }

    public function getRelated(Product $product, int $limit = 8): Collection
    {
        return $this->model->with(['translation', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)->active()->take($limit)->get();
    }
}
