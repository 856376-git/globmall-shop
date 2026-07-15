<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'electronics',
                'sort_order' => 1,
                'en' => ['name' => 'Electronics', 'description' => 'Latest gadgets and devices'],
                'zh' => ['name' => '电子产品', 'description' => '最新数码产品与设备'],
                'children' => [
                    ['slug' => 'smartphones', 'en' => ['name' => 'Smartphones'], 'zh' => ['name' => '智能手机']],
                    ['slug' => 'laptops', 'en' => ['name' => 'Laptops'], 'zh' => ['name' => '笔记本电脑']],
                    ['slug' => 'accessories', 'en' => ['name' => 'Accessories'], 'zh' => ['name' => '配件']],
                ],
            ],
            [
                'slug' => 'fashion',
                'sort_order' => 2,
                'en' => ['name' => 'Fashion', 'description' => 'Trendy clothing and accessories'],
                'zh' => ['name' => '时尚服饰', 'description' => '潮流服装与配饰'],
                'children' => [
                    ['slug' => 'mens', 'en' => ['name' => "Men's"], 'zh' => ['name' => '男装']],
                    ['slug' => 'womens', 'en' => ['name' => "Women's"], 'zh' => ['name' => '女装']],
                    ['slug' => 'shoes', 'en' => ['name' => 'Shoes'], 'zh' => ['name' => '鞋类']],
                ],
            ],
            [
                'slug' => 'home-garden',
                'sort_order' => 3,
                'en' => ['name' => 'Home & Garden', 'description' => 'Furniture and home decor'],
                'zh' => ['name' => '家居园艺', 'description' => '家具与家居装饰'],
                'children' => [
                    ['slug' => 'furniture', 'en' => ['name' => 'Furniture'], 'zh' => ['name' => '家具']],
                    ['slug' => 'kitchen', 'en' => ['name' => 'Kitchen'], 'zh' => ['name' => '厨房用品']],
                ],
            ],
            [
                'slug' => 'sports',
                'sort_order' => 4,
                'en' => ['name' => 'Sports & Outdoors', 'description' => 'Gear for every adventure'],
                'zh' => ['name' => '运动户外', 'description' => '每次冒险的装备'],
                'children' => [
                    ['slug' => 'fitness', 'en' => ['name' => 'Fitness'], 'zh' => ['name' => '健身']],
                    ['slug' => 'camping', 'en' => ['name' => 'Camping'], 'zh' => ['name' => '露营']],
                ],
            ],
            [
                'slug' => 'beauty',
                'sort_order' => 5,
                'en' => ['name' => 'Beauty & Health', 'description' => 'Skincare, makeup and wellness'],
                'zh' => ['name' => '美妆健康', 'description' => '护肤、彩妆与养生'],
                'children' => [
                    ['slug' => 'skincare', 'en' => ['name' => 'Skincare'], 'zh' => ['name' => '护肤']],
                    ['slug' => 'makeup', 'en' => ['name' => 'Makeup'], 'zh' => ['name' => '彩妆']],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $enData = $catData['en'];
            $zhData = $catData['zh'];
            unset($catData['en'], $catData['zh']);

            $category = Category::create($catData);

            CategoryTranslation::create(array_merge($enData, [
                'category_id' => $category->id,
                'locale' => 'en',
            ]));
            CategoryTranslation::create(array_merge($zhData, [
                'category_id' => $category->id,
                'locale' => 'zh',
            ]));

            foreach ($children as $childData) {
                $childEn = $childData['en'];
                $childZh = $childData['zh'];
                unset($childData['en'], $childData['zh']);

                $child = Category::create(array_merge($childData, [
                    'parent_id' => $category->id,
                    'sort_order' => 0,
                ]));

                CategoryTranslation::create(array_merge($childEn, ['category_id' => $child->id, 'locale' => 'en']));
                CategoryTranslation::create(array_merge($childZh, ['category_id' => $child->id, 'locale' => 'zh']));
            }
        }
    }
}