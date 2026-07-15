<?php
$c = file_get_contents('app/Http/Controllers/UserController.php');

// 1. Add RecentlyViewed import
if (strpos($c, 'use App\Models\RecentlyViewed;') === false) {
    $c = str_replace(
        "use App\Services\SendSmsVerify;",
        "use App\Models\RecentlyViewed;\nuse App\Services\SendSmsVerify;",
        $c
    );
}

// 2. Patch index() to include recentlyViewed
$old = "    public function index(): View
    {
        \$user = auth()->user();
        \$recentOrders = Order::where('user_id', \$user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('account', compact('user', 'recentOrders'));
    }";

$new = "    public function index(): View
    {
        \$user = auth()->user();
        \$recentOrders = Order::where('user_id', \$user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        \$recentlyViewed = RecentlyViewed::with(['product.translation', 'product.primaryImage'])
            ->where('user_id', \$user->id)
            ->orderBy('viewed_at', 'desc')
            ->take(6)
            ->get()
            ->pluck('product')
            ->filter();

        return view('account', compact('user', 'recentOrders', 'recentlyViewed'));
    }";

if (strpos($c, 'recentlyViewed') === false) {
    $c = str_replace($old, $new, $c);
}

// 3. Add recentlyViewed() method after index()
$method = '

    // 最近浏览页面
    public function recentlyViewed(): View
    {
        $recentlyViewed = RecentlyViewed::with(['product.translation', 'product.primaryImage', 'product.images'])
            ->where('user_id', auth()->id())
            ->orderBy('viewed_at', 'desc')
            ->take(24)
            ->get()
            ->pluck('product')
            ->filter();

        return view('recently-viewed', compact('recentlyViewed'));
    }';

if (strpos($c, 'recentlyViewed()') === false) {
    // Insert after index()
    $c = preg_replace('/(    \/\/ 订单列表\s*\n    public function orders)/s', $method . "\n\n\$1", $c);
}

file_put_contents('app/Http/Controllers/UserController.php', $c);
echo "Patched UserController\n";
