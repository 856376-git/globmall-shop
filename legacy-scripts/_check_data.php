<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Category;

// 检查当前商品数和分类数
$productCount = Product::count();
$categoryCount = Category::count();
$featuredCount = Product::where('is_featured', true)->count();
$newCount = Product::where('is_new', true)->count();

echo "Products: {$productCount}\n";
echo "Categories: {$categoryCount}\n";
echo "Featured: {$featuredCount}\n";
echo "New: {$newCount}\n";

// 列出所有分类
$categories = Category::all();
foreach ($categories as $cat) {
    $translation = $cat->translation;
    $name = $translation ? $translation->name : $cat->slug;
    $prodCount = Product::where('category_id', $cat->id)->count();
    echo "Cat: {$name} (ID:{$cat->id}) - {$prodCount} products\n";
}
