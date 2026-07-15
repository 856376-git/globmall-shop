<?php
$content = file_get_contents('app/Http/Controllers/ProductController.php');

// Add RecentlyViewed import
if (strpos($content, 'use App\Models\RecentlyViewed;') === false) {
    $content = str_replace(
        "use App\Models\RecentlyViewed;\nuse App\Models\Category;",
        "use App\Models\RecentlyViewed;\nuse App\Models\Category;\nuse Illuminate\Support\Facades\DB;",
        $content
    );
}

// Replace show() method to add recently viewed tracking
$oldShow = "    public function show(string \$slug): View
    {
        \$product = Product::with([
            'translation',
            'translations',
            'images',
            'options.values',
            'variants.optionValues',
            'category.translation',
            'brand.translation',
        ])->where('slug', \$slug)->active()->firstOrFail();

        \$relatedProducts = Product::with(['translation', 'primaryImage'])
            ->where('category_id', \$product->category_id)
            ->where('id', '!=', \$product->id)
            ->active()
            ->take(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }";

$newShow = "    public function show(string \$slug): View
    {
        \$product = Product::with([
            'translation',
            'translations',
            'images',
            'options.values',
            'variants.optionValues',
            'category.translation',
            'brand.translation',
        ])->where('slug', \$slug)->active()->firstOrFail();

        \$relatedProducts = Product::with(['translation', 'primaryImage'])
            ->where('category_id', \$product->category_id)
            ->where('id', '!=', \$product->id)
            ->active()
            ->take(4)
            ->get();

        // Track recently viewed for logged-in users
        if (auth()->check()) {
            RecentlyViewed::updateOrCreate(
                ['user_id' => auth()->id(), 'product_id' => \$product->id],
                ['viewed_at' => now()]
            );
        }

        // Get recently viewed products for display
        \$recentlyViewed = collect([]);
        if (auth()->check()) {
            \$recentlyViewed = RecentlyViewed::with(['product.translation', 'product.primaryImage'])
                ->where('user_id', auth()->id())
                ->where('product_id', '!=', \$product->id)
                ->orderBy('viewed_at', 'desc')
                ->take(6)
                ->get()
                ->pluck('product')
                ->filter();
        }

        return view('product', compact('product', 'relatedProducts', 'recentlyViewed'));
    }";

if (strpos($content, 'recentlyViewed') === false) {
    $content = str_replace($oldShow, $newShow, $content);
    echo "Patched show() method\n";
} else {
    echo "Already has recentlyViewed\n";
}

file_put_contents('app/Http/Controllers/ProductController.php', $content);
echo "Done\n";
