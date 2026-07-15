<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RecentlyViewed;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['translation', 'primaryImage', 'category.translation', 'brand.translation'])
            ->active();

        // 閸掑棛琚粵娑⑩偓?
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 閸濅胶澧濈粵娑⑩偓?
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // 娴犻攱鐗搁崠娲？
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 閹烘帒绨?
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        $categories = Category::with('translation')->active()->topLevel()->orderBy('sort_order')->get();

        return view('shop', compact('products', 'categories'));
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        $products = Product::with(['translation', 'primaryImage', 'brand.translation'])
            ->where('category_id', $category->id)
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::with('translation')->active()->topLevel()->orderBy('sort_order')->get();

        return view('shop', compact('products', 'categories', 'category'));
    }

    public function show(string $slug): View
    {
        $product = Product::with([
            'translation',
            'translations',
            'images',
            'options.values',
            'variants.optionValues',
            'category.translation',
            'brand.translation',
        ])->where('slug', $slug)->active()->firstOrFail();

        $relatedProducts = Product::with(['translation', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }

    public function search(Request $request): View
    {
        $keyword = $request->input('q');

        $products = Product::with(['translation', 'primaryImage', 'category.translation'])
            ->active()
            ->whereHas('translations', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->orWhere('sku', 'like', "%{$keyword}%")
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::with('translation')->active()->topLevel()->get();

        return view('shop', compact('products', 'categories', 'keyword'));
    }

    public function searchSuggestions(Request $request)
    {
        $keyword = $request->input('q', '');
        $locale = $request->input('locale', app()->getLocale());
        app()->setLocale($locale);

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $products = Product::with(['translation', 'primaryImage'])
            ->active()
            ->whereHas('translations', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->orWhere('sku', 'like', "%{$keyword}%")
            ->take(8)
            ->get();

        $results = $products->map(function ($product) {
            $imgUrl = $product->primaryImage?->image ?? $product->images->first()?->image;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => number_format($product->price, 2),
                'image' => $imgUrl ? asset($imgUrl) : null,
                'url' => route('product.show', $product->slug),
            ];
        });

        return response()->json($results);
    }
}
