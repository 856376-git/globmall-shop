<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 清空
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        ProductTranslation::truncate();
        ProductImage::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = Category::all();
        $brands = Brand::all();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->warn('Categories or brands are empty. Please seed them first.');
            return;
        }

        $productData = [
            ['Premium Wireless Headphones', '高级无线耳机', '高保真无线降噪耳机', 299.99],
            ['Smart Fitness Watch', '智能运动手表', '追踪锻炼和健康数据', 199.99],
            ['Laptop Stand Aluminum', '铝合金笔记本支架', '人体工学，提升散热', 59.99],
            ['Mechanical Keyboard RGB', '机械键盘 RGB', '清脆手感，可自定义背光', 129.99],
            ['Wireless Mouse Ergonomic', '人体工学无线鼠标', '流畅追踪，长续航', 49.99],
            ['USB-C Hub Multiport', 'USB-C 扩展坞', '7合一多功能扩展坞', 79.99],
            ['Portable SSD 1TB', '便携式 SSD', '超快读写，1TB容量', 159.99],
            ['Webcam 1080p HD', '高清摄像头', '1080p自动对焦', 89.99],
            ['Bluetooth Speaker', '蓝牙音箱', '便携蓝牙音箱，重低音', 79.99],
            ['Gaming Mouse Pad', '游戏鼠标垫', '超大尺寸，顺滑表面', 19.99],
            ['Type-C Cable Braided', '编织 Type-C 线', '耐用编织材质，快充', 12.99],
            ['Phone Ring Holder', '手机指环支架', '金属材质，360度旋转', 9.99],
            ['Car Charger USB', '车载充电器', '双USB口，快速充电', 24.99],
            ['Tripod Stand', '三脚架', '铝合金材质，高度可调', 34.99],
            ['Selfie Stick', '自拍杆', '蓝牙遥控，可伸缩', 14.99],
        ];

        $index = 1;
        foreach ($categories as $cat) {
            $brand = $brands->random();
            $count = rand(8, 10);

            for ($i = 0; $i < $count; $i++) {
                $data = $productData[$i % count($productData)];
                $nameEn = $data[0] . ' ' . Str::random(3);
                $nameZh = $data[1] . ' ' . rand(100, 999);
                $slug = Str::slug($nameEn) . '-' . $index;

                $price = $data[3] + rand(0, 50);
                $stock = rand(50, 500);

                $product = Product::create([
                    'category_id' => $cat->id,
                    'brand_id' => $brand->id,
                    'slug' => $slug,
                    'price' => $price,
                    'cost' => round($price * 0.4, 2),
                    'stock' => $stock,
                    'low_stock_threshold' => 10,
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'status' => 'active',
                    'is_featured' => $i < 3,
                    'sort_order' => rand(1, 100),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);

                // 英文翻译
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => 'en',
                    'name' => $nameEn,
                    'description' => $data[2] . ' - Premium quality product',
                    'short_desc' => $data[2],
                    'meta_title' => $nameEn,
                    'meta_description' => $data[2],
                ]);

                // 中文翻译
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => 'zh',
                    'name' => $nameZh,
                    'description' => $data[2] . ' - 高品质产品',
                    'short_desc' => $data[2],
                    'meta_title' => $nameZh,
                    'meta_description' => $data[2],
                ]);

                // 添加图片
                for ($j = 1; $j <= 4; $j++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => 'dae0773b34c6f68983530ed76b8561d5/p' . rand(1, 20) . '.jpg',
                        'path_suffix' => 'p' . rand(1, 20) . '.jpg',
                        'alt' => $nameEn . ' image ' . $j,
                        'is_primary' => $j === 1,
                    ]);
                }

                $index++;
            }
        }

        $this->command->info('ProductSeeder completed! Created ' . $index . ' products.');
    }
}