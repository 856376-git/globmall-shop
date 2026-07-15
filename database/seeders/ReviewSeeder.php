<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['iphone-15-pro', 5, 'Amazing phone!', 'The titanium build feels premium. Camera quality is outstanding.'],
            ['iphone-15-pro', 5, 'Best iPhone yet', 'Upgraded from 13 Pro and the difference is noticeable. Fast and smooth.'],
            ['iphone-15-pro', 4, 'Great but expensive', 'Love the phone but the price is steep. USB-C is a welcome change.'],
            ['samsung-galaxy-s24-ultra', 5, 'Galaxy AI is impressive', 'The AI features are genuinely useful. S Pen integration is perfect.'],
            ['samsung-galaxy-s24-ultra', 4, 'Big and powerful', 'Amazing display and camera. A bit heavy for one-handed use.'],
            ['macbook-pro-14-m3', 5, 'Perfect for development', 'Compiles my projects in seconds. Battery lasts all day.'],
            ['macbook-pro-14-m3', 5, 'Worth every penny', 'The screen is gorgeous. M3 chip is a beast for video editing.'],
            ['sony-wh-1000xm5', 5, 'Noise cancellation is magic', 'Wore these on a 10-hour flight and barely heard the engines.'],
            ['sony-wh-1000xm5', 4, 'Great sound', 'Sound quality is excellent but they are a bit tight at first.'],
            ['nike-air-force-1-white', 5, 'Classic never fails', 'Clean look, comfortable fit. Goes with everything.'],
            ['nike-air-force-1-white', 4, 'Good but runs big', 'Classic style but order half size down.'],
            ['adidas-superstar', 5, 'Timeless design', 'The shell toe is iconic. Comfortable for daily wear.'],
            ['ikea-kallax-shelf', 4, 'Functional and affordable', 'Easy to assemble, fits perfectly in my living room.'],
            ['premium-espresso-machine', 5, 'Cafe quality at home', 'Makes better espresso than my local coffee shop.'],
            ['premium-yoga-mat', 5, 'Perfect grip', 'Non-slip even during intense sessions. Very comfortable.'],
            ['hyaluronic-acid-serum', 5, 'Skin feels amazing', 'Lightweight and absorbs quickly. My skin looks plumper.'],
            ['hyaluronic-acid-serum', 4, 'Good hydration', 'Works well under moisturizer. Wish the bottle was bigger.'],
        ];

        $customer = User::where('email', 'john@test.com')->first();
        $admin = User::where('email', 'admin@globmall.com')->first();
        $users = [$customer, $admin];

        foreach ($reviews as $index => $review) {
            $product = Product::where('slug', $review[0])->first();
            if (!$product) continue;

            Review::create([
                'product_id' => $product->id,
                'user_id' => $users[$index % 2]->id,
                'rating' => $review[1],
                'title' => $review[2],
                'content' => $review[3],
                'is_approved' => true,
            ]);
        }

        echo "Reviews seeded: " . count($reviews) . "\n";
    }
}