<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $products = Product::with(['translation', 'category.translation', 'brand.translation'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::with('translation')->active()->topLevel()->get();
        $brands = Brand::with('translation')->active()->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->input('en.name'));
        $count = Product::where('slug', 'LIKE', "$slug%")->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $product = Product::create([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'sku' => $request->sku,
            'slug' => $slug,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'cost' => $request->cost ?? 0,
            'stock' => $request->stock,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'weight' => $request->weight ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_new' => $request->boolean('is_new'),
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            ProductTranslation::create([
                'product_id' => $product->id,
                'locale' => $locale,
                'name' => $request->input("$locale.name"),
                'description' => $request->input("$locale.description"),
                'short_description' => $request->input("$locale.short_description"),
                'meta_title' => $request->input("$locale.meta_title"),
                'meta_description' => $request->input("$locale.meta_description"),
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $filename = $product->slug . '-' . time() . '-' . $index . '.' . $file->extension();
                $file->move(public_path('uploads/products'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'uploads/products/' . $filename,
                    'alt_text' => $request->input('en.name'),
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['translations', 'images']);
        $categories = Category::with('translation')->active()->topLevel()->get();
        $brands = Brand::with('translation')->active()->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,webp|max:2048',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'sku' => $request->sku,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'cost' => $request->cost ?? 0,
            'stock' => $request->stock,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'weight' => $request->weight ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_new' => $request->boolean('is_new'),
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            ProductTranslation::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $locale],
                [
                    'name' => $request->input("$locale.name"),
                    'description' => $request->input("$locale.description"),
                    'short_description' => $request->input("$locale.short_description"),
                    'meta_title' => $request->input("$locale.meta_title"),
                    'meta_description' => $request->input("$locale.meta_description"),
                ]
            );
        }

        if ($request->hasFile('images')) {
            $maxOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $file) {
                $filename = $product->slug . '-' . time() . '-' . $index . '.' . $file->extension();
                $file->move(public_path('uploads/products'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'uploads/products/' . $filename,
                    'alt_text' => $request->input('en.name'),
                    'sort_order' => $maxOrder + $index + 1,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            $path = public_path($image->image);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function deleteImage(int $id): RedirectResponse
    {
        $image = ProductImage::findOrFail($id);
        $path = public_path($image->image);
        if (file_exists($path)) {
            @unlink($path);
        }
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }
}

