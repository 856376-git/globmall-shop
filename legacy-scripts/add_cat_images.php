<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$catImages = [
    'electronics' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661',
    'fashion' => 'https://images.unsplash.com/photo-1445205170230-053b83016050',
    'home-garden' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba',
    'sports' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b',
    'beauty' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348',
];

foreach ($catImages as $slug => $url) {
    Category::where('slug', $slug)->update(['image' => $url]);
    echo "Category image: $slug\n";
}

echo "Done!\n";