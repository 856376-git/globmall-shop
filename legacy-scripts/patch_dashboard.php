<?php
$c = file_get_contents('app/Http/Controllers/Admin/DashboardController.php');

$new = '<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where("status", "!=", "cancelled")->sum("total");
        $totalProducts = Product::count();
        $totalUsers = User::where("role", "customer")->count();
        $totalReviews = Review::count();
        $avgRating = round(Review::avg("rating") ?? 0, 1);
        $lowStockCount = Product::whereColumn("stock", "<=", "low_stock_threshold")->count();

        $recentOrders = Order::with("user")->orderBy("created_at", "desc")->take(8)->get();
        $lowStockProducts = Product::whereColumn("stock", "<=", "low_stock_threshold")->take(8)->get();

        // 7-day sales trend
        $salesTrend = Order::selectRaw("DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders")
            ->where("created_at", ">=", now()->subDays(7))
            ->where("status", "!=", "cancelled")
            ->groupBy("date")
            ->orderBy("date")
            ->get();

        // Order status distribution
        $statusDist = Order::selectRaw("status, COUNT(*) as cnt")
            ->groupBy("status")
            ->pluck("cnt", "status");

        // Top selling products
        $topProducts = Product::select("products.*", DB::raw("COALESCE(SUM(order_items.quantity),0) as sold"))
            ->leftJoin("order_items", "products.id", "=", "order_items.product_id")
            ->groupBy("products.id")
            ->orderByDesc("sold")
            ->take(5)
            ->get();

        // Category distribution
        $catDist = Product::selectRaw("categories.name as cat, COUNT(*) as cnt")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->groupBy("categories.id")
            ->orderByDesc("cnt")
            ->take(6)
            ->get();

        return view("admin.dashboard", compact(
            "totalOrders", "totalRevenue", "totalProducts", "totalUsers",
            "totalReviews", "avgRating", "lowStockCount",
            "recentOrders", "lowStockProducts",
            "salesTrend", "statusDist", "topProducts", "catDist"
        ));
    }
}';

file_put_contents('app/Http/Controllers/Admin/DashboardController.php', $new);
echo "DashboardController upgraded\n";
