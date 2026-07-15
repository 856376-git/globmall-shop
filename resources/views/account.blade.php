@extends('layouts.app')

@section('title', __('messages.my_account'))

@push('styles')
<style>
    .account-hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 30px; margin-bottom: 2rem; color: #fff; display: flex; align-items: center; gap: 20px; }
    .account-hero .avatar { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; }
    .account-hero h2 { margin: 0; font-weight: 800; }
    .account-hero .email { opacity: 0.9; margin-bottom: 0; }
    .account-menu { background: #fff; border-radius: 16px; padding: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
    .account-menu a { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 10px; color: #333; text-decoration: none; transition: all .2s; }
    .account-menu a:hover { background: #f8f9ff; color: #667eea; }
    .account-menu a.active { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
    .account-menu i { font-size: 1.2rem; }
    .order-card { border: 1px solid #f0f0f0; border-radius: 12px; padding: 16px; margin-bottom: 12px; transition: all .2s; }
    .order-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .order-status { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-delivered { background: #28a745; color: #fff; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .stat-card .num { font-size: 2rem; font-weight: 800; color: #667eea; }
    .stat-card .label { color: #888; font-size: 0.9rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Hero -->
    <div class="account-hero">
        <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
            <h2>{{ $user->name }}</h2>
            <p class="email">{{ $user->email }}</p>
            <span class="badge bg-light text-primary">{{ __('messages.role_'.$user->role) }}</span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Menu -->
        <div class="col-md-3">
            <div class="account-menu">
                <a href="{{ route('account') }}" class="active"><i class="bi bi-person-fill"></i> {{ __('messages.my_profile') }}</a>
                <a href="{{ route('orders') }}"><i class="bi bi-bag-fill"></i> {{ __('messages.my_orders') }}</a>
                <a href="{{ route('addresses') }}"><i class="bi bi-geo-alt-fill"></i> {{ __('messages.addresses') }}</a>
                <a href="{{ route('wishlist') }}"><i class="bi bi-heart-fill"></i> {{ __('messages.wishlist') }}</a>
                <a href="{{ route('recently-viewed') }}"><i class="bi bi-clock-history"></i> {{ __('messages.recently_viewed') }}</a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> {{ __('messages.logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-md-9">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="num">{{ $user->orders()->count() }}</div>
                        <div class="label">{{ __('messages.orders') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="num">{{ $user->wishlist()->count() }}</div>
                        <div class="label">{{ __('messages.wishlist') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="num">{{ $user->reviews()->count() }}</div>
                        <div class="label">{{ __('messages.reviews') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="num">{{ $user->addresses()->count() }}</div>
                        <div class="label">{{ __('messages.addresses') }}</div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <h5 class="fw-bold mb-3">{{ __('messages.recent_orders') }}</h5>
            @if($recentOrders->count())
            @foreach($recentOrders as $order)
            <a href="{{ route('order.detail', $order->order_no) }}" class="order-card d-block text-decoration-none text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $order->order_no }}</div>
                        <div class="text-muted small">{{ $order->created_at->format('M d, Y') }} &bull; {{ $order->items->count() }} {{ __('messages.items') }}</div>
                    </div>
                    <div class="text-end">
                        <span class="order-status status-{{ $order->status }}">{{ __('messages.'.$order->status) }}</span>
                        <div class="fw-bold fs-5 mt-1">${{ number_format($order->total, 2) }}</div>
                    </div>
                </div>
            </a>
            @endforeach
            <a href="{{ route('orders') }}" class="btn btn-outline-primary btn-sm mt-3">{{ __('messages.view_all_orders') }}</a>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-bag-x" style="font-size:3rem;"></i>
                <p class="mt-2">{{ __('messages.no_orders_yet') }}</p>
                <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">{{ __('messages.start_shopping') }}</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection