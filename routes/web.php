<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartApiController;
use App\Http\Controllers\ReviewController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/shop', [ProductController::class, 'index'])->name('shop');
    Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/search', [ProductController::class, 'search'])->name('search');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/api/coupon/apply', function () {
        $request = request();
        $request->validate(['code' => 'required|string']);
        $coupon = App\Models\Coupon::where('code', $request->code)->first();
        if (!$coupon) return response()->json(['error' => 'Invalid coupon code'], 404);
        if (!$coupon->isValid()) return response()->json(['error' => 'Coupon expired or used up'], 422);
        $items = App\Models\CartItem::where('user_id', auth()->id())->get();
        $subtotal = $items->sum('subtotal');
        $discount = $coupon->calculateDiscount($subtotal);
        return response()->json([
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount' => $discount,
            'message' => 'Coupon applied successfully'
        ]);
    })->name('coupon.apply');
    Route::get('/api/cart/count', [CartApiController::class, 'count'])->name('cart.count');

    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
        Route::get('/checkout/success/{order_no}', [CheckoutController::class, 'success'])->name('checkout.success');

        Route::get('/account', [UserController::class, 'index'])->name('account');
        Route::get('/orders', [UserController::class, 'orders'])->name('orders');
        Route::get('/orders/{order_no}', [UserController::class, 'orderDetail'])->name('order.detail');
        Route::get('/addresses', [UserController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [UserController::class, 'storeAddress'])->name('addresses.store');
        Route::post('/addresses/{id}/default', [UserController::class, 'setDefaultAddress'])->name('addresses.default');
        Route::delete('/addresses/{id}', [UserController::class, 'deleteAddress'])->name('addresses.delete');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::get('/recently-viewed', [UserController::class, 'recentlyViewed'])->name('recently-viewed');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

        Route::get('/payment/{order_no}', [StripePaymentController::class, 'createCheckoutSession'])->name('stripe.checkout');
        Route::get('/payment/{order_no}/success', [StripePaymentController::class, 'success'])->name('stripe.success');
    });

    Route::get('/login', [UserController::class, 'loginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->middleware('recaptcha');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/register', [UserController::class, 'registerForm'])->name('register');
    Route::post('/register', [UserController::class, 'register'])->middleware('recaptcha');
    Route::post('/sms/send', [UserController::class, 'sendSmsCode'])->name('sms.send');
});

// Search Autocomplete API (outside locale group, locale passed via query string)
Route::get('/api/search/suggestions', [App\Http\Controllers\ProductController::class, 'searchSuggestions'])->name('api.search.suggestions');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super-admin,admin,operations,customer-service,viewer'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->middleware(['permission:category.view', 'permission:category.create|permission:category.edit|permission:category.delete']);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->middleware(['permission:product.view', 'permission:product.create|permission:product.edit|permission:product.delete']);
    Route::delete('/products/images/{id}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->middleware('permission:product.edit')->name('products.deleteImage');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->middleware(['permission:order.view', 'permission:order.update|permission:order.delete']);
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->middleware('permission:order.update')->name('orders.updateStatus');
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class)->middleware(['permission:coupon.view', 'permission:coupon.create|permission:coupon.edit|permission:coupon.delete']);
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class)->middleware(['permission:brand.view', 'permission:brand.create|permission:brand.edit|permission:brand.delete']);
    Route::resource('reviews', App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'destroy'])->middleware(['permission:review.view', 'permission:review.delete']);
    Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->middleware('permission:review.moderate')->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->middleware('permission:review.moderate')->name('reviews.reject');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['create', 'store'])->middleware(['permission:user.view', 'permission:user.edit|permission:user.delete']);
    Route::get('users/{user}/roles', [App\Http\Controllers\Admin\UserController::class, 'editRoles'])->middleware('permission:user.edit')->name('users.roles.edit');
    Route::put('users/{user}/roles', [App\Http\Controllers\Admin\UserController::class, 'updateRoles'])->middleware('permission:user.edit')->name('users.roles.update');
        
    // Refunds
    Route::resource('refunds', App\Http\Controllers\Admin\RefundController::class)->only(['index', 'show'])->middleware(['permission:refund.view', 'permission:refund.process']);
    Route::post('refunds/{id}/approve', [App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('refunds.approve');
    Route::post('refunds/{id}/reject', [App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('refunds.reject');
    Route::post('refunds/{id}/complete', [App\Http\Controllers\Admin\RefundController::class, 'complete'])->name('refunds.complete');

    // Members
    Route::get('members', [App\Http\Controllers\Admin\MemberController::class, 'index'])->middleware('permission:member.view')->name('members.index');
    Route::get('members/{userId}/points', [App\Http\Controllers\Admin\MemberController::class, 'points'])->middleware('permission:member.points')->name('members.points');
    Route::post('members/{userId}/points/add', [App\Http\Controllers\Admin\MemberController::class, 'addPoints'])->middleware('permission:member.points')->name('members.addPoints');
    Route::post('members/{userId}/points/deduct', [App\Http\Controllers\Admin\MemberController::class, 'deductPoints'])->middleware('permission:member.points')->name('members.deductPoints');

    Route::resource('system-config', App\Http\Controllers\Admin\SystemConfigController::class)->middleware(['permission:system.manage'])->only(['index', 'store', 'update', 'destroy'])->names(['index' => 'system-config.index', 'store' => 'system-config.store', 'update' => 'system-config.update', 'destroy' => 'system-config.destroy']);

    // RBAC Roles & Permissions
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->middleware(['permission:role.manage']);
    Route::get('permissions', [App\Http\Controllers\Admin\RoleController::class, 'permissions'])->middleware('permission:role.manage')->name('permissions.index');
    Route::get('/export/orders', [App\Http\Controllers\Admin\ExportController::class, 'exportOrders'])->middleware('permission:order.export')->name('export.orders');
});

