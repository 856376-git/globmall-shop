<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\RecentlyViewed;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\SendSmsVerify;

class UserController extends Controller
{
    // 注册页面
    public function registerForm(): View
    {
        return view('auth.register');
    }

    // 处理注册
    public function register(Request $request): RedirectResponse
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];

        // 如果启用了 SMS 验证，增加手机号和验证码规则
        if (config('services.twilio.enabled', false)) {
            $rules['phone'] = 'required|string|max:20';
            $rules['sms_code'] = 'required|string|size:6';
        }

        $request->validate($rules);

        // 如果启用了 SMS 验证，校验验证码
        if (config('services.twilio.enabled', false)) {
            $smsService = app(SendSmsVerify::class);
            try {
                $smsService->verifyCode($request->phone, $request->sms_code);
            } catch (\RuntimeException $e) {
                return back()->withErrors(['sms_code' => $e->getMessage()])->withInput();
            }
        }

        $userData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
            'status'   => 1,
        ];

        if (config('services.twilio.enabled', false)) {
            $userData['phone'] = $request->phone;
        }

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Welcome to GlobMall!');
    }

    // 发送 SMS 验证码（AJAX 接口）
    public function sendSmsCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        if (!config('services.twilio.enabled', false)) {
            return response()->json(['success' => false, 'message' => 'SMS verification is not enabled.'], 400);
        }

        // 防刷：60秒内同一手机号只能发一次
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $lastSent = Cache::get("sms_throttle:{$phone}");
        if ($lastSent && (time() - $lastSent) < 60) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 60 seconds before requesting another code.'
            ], 429);
        }

        try {
            $smsService = app(SendSmsVerify::class);
            $smsService->sendVerification($request->phone);
            Cache::put("sms_throttle:{$phone}", time(), 120);
            return response()->json(['success' => true, 'message' => 'Verification code sent.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 登录页面
    public function loginForm(): View
    {
        return view('auth.login');
    }

    // 处理登录
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // 退出登录
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // 用户中心
    public function index()
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $user = auth()->user();
        $recentlyViewed = RecentlyViewed::with(['product.translation', 'product.primaryImage'])
            ->where('user_id', $user->id)
            ->orderBy('viewed_at', 'desc')
            ->take(6)
            ->get()
            ->pluck('product')
            ->filter();

        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('account', compact('user', 'recentlyViewed', 'recentOrders'));
    }

    // 订单列表
    public function orders()
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders', compact('orders'));
    }

    // 订单详情
    public function orderDetail(string $orderNo)
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $order = Order::with(['items.product', 'address'])
            ->where('order_no', $orderNo)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('order-detail', compact('order'));
    }

    // 地址列表
    public function addresses()
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $addresses = Address::where('user_id', auth()->id())->get();
        return view('addresses', compact('addresses'));
    }

    // 新增地址
    public function storeAddress(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'country'        => 'required|string|max:10',
            'state'          => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'address_line1'  => 'required|string|max:255',
            'address_line2'  => 'nullable|string|max:255',
            'zipcode'        => 'required|string|max:20',
            'label'          => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        if ($request->boolean('is_default')) {
            Address::where('user_id', auth()->id())->update(['is_default' => 0]);
            $data['is_default'] = 1;
        }

        Address::create($data);

        return back()->with('success', 'Address saved successfully.');
    }

    // 设为默认地址
    public function setDefaultAddress(int $id): RedirectResponse
    {
        Address::where('user_id', auth()->id())->update(['is_default' => 0]);
        Address::where('user_id', auth()->id())->where('id', $id)->update(['is_default' => 1]);

        return back()->with('success', 'Default address updated.');
    }

    // 删除地址
    public function deleteAddress(int $id): RedirectResponse
    {
        Address::where('user_id', auth()->id())->where('id', $id)->delete();
        return back()->with('success', 'Address deleted.');
    }

    // 最近浏览
    public function recentlyViewed()
    {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        $recentlyViewed = RecentlyViewed::with(['product.translation', 'product.primaryImage'])
            ->where('user_id', auth()->id())
            ->orderBy('viewed_at', 'desc')
            ->take(12)
            ->get()
            ->pluck('product')
            ->filter();

        return view('recently-viewed', compact('recentlyViewed'));
    }

}
