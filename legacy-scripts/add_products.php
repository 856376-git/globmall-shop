<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Str;

// Get categories
$cats = Category::pluck('id')->toArray();

// Product names for different categories
$productNames = [
    'Electronics' => ['Wireless Earbuds Pro', 'Smart Watch Ultra', 'Bluetooth Speaker', 'USB-C Hub', 'Mechanical Keyboard', 'Gaming Mouse', 'Webcam HD', 'Monitor Stand', 'Laptop Cooler', 'Phone Holder'],
    'Fashion' => ['Classic T-Shirt', 'Slim Fit Jeans', 'Running Shoes', 'Leather Belt', 'Canvas Backpack', 'Sunglasses', 'Baseball Cap', 'Wool Sweater', 'Sports Leggings', 'Winter Jacket'],
    'Home' => ['LED Desk Lamp', 'Coffee Maker', 'Air Purifier', 'Throw Blanket', 'Wall Art Set', 'Storage Box', 'Bamboo Utensils', 'Scented Candle', 'Plant Pot', 'Door Mat'],
    'Beauty' => ['Vitamin C Serum', 'Face Moisturizer', 'Hair Dryer', 'Makeup Brush Set', 'Perfume Set', 'Nail Polish Kit', 'Bath Bombs', 'Lip Care Set', 'Eye Cream', 'Body Lotion'],
    'Sports' => ['Yoga Mat', 'Resistance Bands', 'Water Bottle', 'Jump Rope', 'Fitness Tracker', 'Dumbbells Set', 'Foam Roller', 'Sports Bag', 'Ankle Weights', 'Exercise Ball'],
];

$desc = 'Premium quality product with excellent craftsmanship. Perfect for everyday use with modern design and durable materials.';
$shortDesc = 'High-quality product at an amazing price.';

$faker = \Faker\Factory::create();

for ($i = 0; $i < 100; $i++) {
    $catIndex = $i % count($cats);
    $categoryId = $cats[$catIndex];
    
    // Get category name to pick appropriate product names
    $category = Category::find($categoryId);
    $catName = $category ? $category->name : 'Electronics';
    
    // Pick random product name from appropriate category
    $namePool = $productNames['Electronics']; // default
    foreach ($productNames as $cat => $names) {
        if (stripos($catName, $cat) !== false) {
            $namePool = $names;
            break;
        }
    }
    
    $name = $namePool[$i % count($namePool)] . ' - Model ' . ($i + 1);
    $slug = Str::slug($name . '-' . ($i + 200));
    
    $price = rand(20, 500);
    $comparePrice = $price + rand(20, 100);
    $stock = rand(10, 200);
    
    $product = Product::create([
        'category_id' => $categoryId,
        'sku' => 'SKU-' . ($i + 200),
        'slug' => $slug,
        'price' => $price,
        'compare_price' => $i % 3 == 0 ? $comparePrice : null,
        'cost' => $price * 0.6,
        'stock' => $stock,
        'low_stock_threshold' => 5,
        'weight' => rand(1, 50) / 10,
        'is_featured' => $i < 20 ? 1 : 0,
        'is_new' => $i < 15 ? 1 : 0,
        'status' => 1,
    ]);
    
    ProductTranslation::create([
        'product_id' => $product->id,
        'locale' => 'en',
        'name' => $name,
        'description' => $desc,
        'short_description' => $shortDesc,
    ]);
    
    ProductTranslation::create([
        'product_id' => $product->id,
        'locale' => 'zh',
        'name' => '优质商品 ' . ($i + 200),
        'description' => '高品质产品，精心设计，经久耐用。',
        'short_description' => '优质商品，价格实惠。',
    ]);
    
    // Add images using picsum
    $numImages = rand(2, 4);
    for ($j = 0; $j < $numImages; $j++) {
        ProductImage::create([
            'product_id' => $product->id,
            'image' => 'https://picsum.photos/seed/' . ($i * 10 + $j) . '/800/800',
            'is_primary' => $j === 0 ? 1 : 0,
            'sort_order' => $j,
        ]);
    }
    
    if ($i % 20 == 0) {
        echo "Created {$i} products...\n";
    }
}

echo "Total products now: " . Product::count() . "\n";
