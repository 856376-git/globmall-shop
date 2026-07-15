<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
    ) {}

    public function index(): View
    {
        $homeData = $this->productRepo->getHomePageData();

        $categories = Category::with(['translation', 'children.translation'])
            ->active()->topLevel()->orderBy('sort_order')->get();

        $brands = Brand::with('translation')->active()->take(12)->get();

        $coupons = Coupon::where('status', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->take(4)->get();

        $reviews = Review::with(['user', 'product.translation'])
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->take(8)->get();

        return view('home', array_merge($homeData, compact(
            'categories', 'brands', 'coupons', 'reviews'
        )));
    }
}
