<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandTranslation;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['slug' => 'apple', 'sort_order' => 1, 'en' => ['name' => 'Apple', 'description' => 'Think Different'], 'zh' => ['name' => '苹果', 'description' => '非同凡想']],
            ['slug' => 'samsung', 'sort_order' => 2, 'en' => ['name' => 'Samsung', 'description' => 'Galaxy of possibilities'], 'zh' => ['name' => '三星', 'description' => '无限可能']],
            ['slug' => 'nike', 'sort_order' => 3, 'en' => ['name' => 'Nike', 'description' => 'Just Do It'], 'zh' => ['name' => '耐克', 'description' => '放手去做']],
            ['slug' => 'adidas', 'sort_order' => 4, 'en' => ['name' => 'Adidas', 'description' => 'Impossible Is Nothing'], 'zh' => ['name' => '阿迪达斯', 'description' => '没有不可能']],
            ['slug' => 'sony', 'sort_order' => 5, 'en' => ['name' => 'Sony', 'description' => 'Be Moved'], 'zh' => ['name' => '索尼', 'description' => '感动常在']],
            ['slug' => 'ikea', 'sort_order' => 6, 'en' => ['name' => 'IKEA', 'description' => 'Design for everyone'], 'zh' => ['name' => '宜家', 'description' => '为大众设计']],
        ];

        foreach ($brands as $brandData) {
            $enData = $brandData['en'];
            $zhData = $brandData['zh'];
            unset($brandData['en'], $brandData['zh']);

            $brand = Brand::create($brandData);

            BrandTranslation::create(array_merge($enData, ['brand_id' => $brand->id, 'locale' => 'en']));
            BrandTranslation::create(array_merge($zhData, ['brand_id' => $brand->id, 'locale' => 'zh']));
        }
    }
}