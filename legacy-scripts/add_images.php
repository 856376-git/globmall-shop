<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;

// 每个商品对应的Unsplash图片URL
$images = [
    'iphone-15-pro' => ['https://images.unsplash.com/photo-1592750475338-74b7b21085ab', 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5'],
    'samsung-galaxy-s24-ultra' => ['https://images.unsplash.com/photo-1610945265064-0e34e5519bbf', 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0'],
    'macbook-pro-14-m3' => ['https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853'],
    'sony-wh-1000xm5' => ['https://images.unsplash.com/photo-1583394838336-acd977736f90', 'https://images.unsplash.com/photo-1546435770-a3e426bf472b'],
    'nike-air-force-1-white' => ['https://images.unsplash.com/photo-1542291026-7eec264c27ff', 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519'],
    'adidas-superstar' => ['https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2', 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa'],
    'ikea-kallax-shelf' => ['https://images.unsplash.com/photo-1555041469-a586c61ea9bc', 'https://images.unsplash.com/photo-1598063183638-7145386e7617'],
    'premium-espresso-machine' => ['https://images.unsplash.com/photo-1572119865084-43c285814d63', 'https://images.unsplash.com/photo-1517663154411-c85b87f37a68'],
    'premium-yoga-mat' => ['https://images.unsplash.com/photo-1592432678016-e910b452f9a2', 'https://images.unsplash.com/photo-1599447421416-3414b3a0d36b'],
    'hyaluronic-acid-serum' => ['https://images.unsplash.com/photo-1620916566398-39f1143ab7be', 'https://images.unsplash.com/photo-1556228720-195a672e8a03'],
];

foreach ($images as $slug => $urls) {
    $product = Product::where('slug', $slug)->first();
    if (!$product) continue;

    // 先清掉旧图片记录
    ProductImage::where('product_id', $product->id)->delete();

    foreach ($urls as $index => $url) {
        ProductImage::create([
            'product_id' => $product->id,
            'image' => $url,
            'alt_text' => $product->translations->where('locale', 'en')->first()?->name,
            'sort_order' => $index,
            'is_primary' => $index === 0,
        ]);
    }
    echo "Images added for: $slug\n";
}

echo "Done!\n";