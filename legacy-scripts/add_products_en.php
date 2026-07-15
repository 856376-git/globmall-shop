<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Str;

$cats = Category::pluck('id')->toArray();

$names = [
    'Wireless Earbuds Pro', 'Smart Watch Ultra', 'Bluetooth Speaker', 'USB-C Hub',
    'Mechanical Keyboard', 'Gaming Mouse', 'Webcam HD', 'Monitor Stand',
    'Laptop Cooler', 'Phone Holder', 'Noise Cancelling Headphones', 'Power Bank 20000mAh',
    'Wireless Charger Pad', 'Tablet Stand', 'Smart Home Hub', 'Action Camera',
    'Portable SSD 1TB', 'LED Strip Lights', 'Car Phone Mount', 'Smart Doorbell',
    'Air Fryer', 'Coffee Grinder', 'Electric Kettle', 'Blender Pro',
    'Slow Cooker', 'Toaster Oven', 'Food Processor', 'Ice Maker',
    'Stand Mixer', 'Espresso Machine', 'Wine Cooler', 'Rice Cooker',
    'Yoga Mat Premium', 'Resistance Bands Set', 'Water Bottle Insulated', 'Jump Rope Pro',
    'Fitness Tracker Band', 'Dumbbells 10kg Set', 'Foam Roller Deep Tissue', 'Sports Duffel Bag',
    'Ankle Weights Pair', 'Exercise Balance Ball', 'Running Belt', 'Swimming Goggles',
    'Tennis Racket Pro', 'Basketball Indoor', 'Cycling Helmet', 'Camping Tent 4P',
    'Hiking Backpack 50L', 'Sleeping Bag', 'Fishing Rod Set', 'Trekking Poles Pair',
    'Classic Cotton T-Shirt', 'Slim Fit Denim Jeans', 'Running Shoes Light', 'Leather Belt Brown',
    'Canvas Messenger Bag', 'Polarized Sunglasses', 'Adjustable Baseball Cap', 'Merino Wool Sweater',
    'Sports Compression Leggings', 'Down Winter Jacket', 'Linen Dress Shirt', 'Chino Shorts',
    'Wool Scarf', 'Leather Wallet', 'Crossbody Purse', 'Sneakers Casual',
    'Silk Tie Set', 'Crew Socks Pack', 'Thermal Underwear', 'Raincoat Packable',
    'Vitamin C Serum 30ml', 'Hyaluronic Acid Moisturizer', 'Hair Dryer Ionic', 'Makeup Brush 10pc Set',
    'Perfume Gift Set', 'Nail Polish Collection', 'Bath Bomb Gift Set', 'Lip Balm Collection',
    'Retinol Eye Cream', 'Shea Body Butter', 'Face Mask Sheet Set', 'Essential Oil Set',
    'Sunscreen SPF50', 'Facial Cleanser', 'Hair Growth Serum', 'Teeth Whitening Kit',
    'LED Desk Lamp Smart', 'Ceramic Coffee Mug Set', 'Bamboo Cutting Board', 'Scented Soy Candle',
    'Indoor Plant Set', 'Woven Throw Blanket', 'Wall Art Canvas Print', 'Ceramic Storage Jars',
    'Silk Pillowcase Set', 'Bath Towel Set', 'Reusable Food Wraps', 'Kitchen Utensil Set',
    'Door Mat Coir', 'Plant Stand Wood', 'Photo Frame Set', 'Decorative Vase Set'
];

$desc = 'Premium quality product with excellent craftsmanship. Perfect for everyday use with modern design and durable materials. Backed by our satisfaction guarantee.';
$short = 'High-quality product at an amazing price.';

echo "Adding products...\n";

for ($i = 0; $i < 100; $i++) {
    $catId = $cats[$i % count($cats)];
    $name = $names[$i % count($names)] . ' - v' . ($i + 1);
    $slug = Str::slug($name);
    $price = rand(15, 500) + (rand(0, 99) / 100);
    $hasCompare = $i % 3 == 0;
    $comparePrice = $hasCompare ? $price * 1.3 : null;

    $product = Product::create([
        'category_id' => $catId,
        'sku' => 'SKU-' . ($i + 200),
        'slug' => $slug,
        'price' => round($price, 2),
        'compare_price' => $hasCompare ? round($comparePrice, 2) : null,
        'cost' => round($price * 0.6, 2),
        'stock' => rand(5, 200),
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
        'short_description' => $short,
    ]);

    $numImages = rand(2, 4);
    for ($j = 0; $j < $numImages; $j++) {
        ProductImage::create([
            'product_id' => $product->id,
            'image' => 'https://picsum.photos/seed/' . ($i * 10 + $j + 999) . '/800/800',
            'is_primary' => $j === 0 ? 1 : 0,
            'sort_order' => $j,
        ]);
    }

    if ($i % 20 == 0) echo "  {$i} done\n";
}

echo "Total products: " . Product::count() . "\n";
