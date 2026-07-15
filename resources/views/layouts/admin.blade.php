<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.dashboard')) - GlobMall Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin-asc.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-body">
<header class="asc-top">
    <a href="{{ route('admin.dashboard') }}" class="asc-logo"><i class="bi bi-globe2"></i> GlobMall</a>
    <div class="asc-merchant">{{ __("messages.central") }}</div>
    <div class="ms-auto"></div>
    @auth
    <div class="asc-user dropdown ms-auto">
        <div data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
            <div>
                <div class="asc-u-s">{{ __("messages.hello") }}</div>
                <div class="asc-u-n">{{ auth()->user()->name }}</div>
            </div>
            <i class="bi bi-chevron-down ms-1"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-shop me-2"></i>{{ __("messages.view_store") }}</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><form action="{{ route('logout') }}" method="POST">@csrf <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>{{ __("messages.logout") }}</button></form></li>
        </ul>
    </div>
    @endauth
</header>
<div class="asc-wrap">
    <aside class="asc-side">
        <nav>
            @php
                $items = [
                    ['admin.dashboard','admin/dashboard*','bi-speedometer2','messages.dashboard'],
                    ['admin.products.index','admin/products*','bi-box-seam','messages.products'],
                    ['admin.categories.index','admin/categories*','bi-tags','messages.category_management'],
                    ['admin.brands.index','admin/brands*','bi-award','messages.brands'],
                    ['admin.orders.index','admin/orders*','bi-receipt','messages.order_management'],
                    ['admin.refunds.index','admin/refunds*','bi-arrow-counterclockwise','messages.refund_requests'],
                    ['admin.members.index','admin/members*','bi-people','messages.members'],
                    ['admin.coupons.index','admin/coupons*','bi-ticket-perforated','messages.coupons'],
                    ['admin.reviews.index','admin/reviews*','bi-chat-square-text','messages.review_management'],
                    ['admin.users.index','admin/users*','bi-person-badge','messages.users'],
                    ['admin.roles.index','admin/roles*','bi-shield-lock','messages.roles'],
                    ['admin.permissions.index','admin/permissions*','bi-key','messages.permissions'],
                    ['admin.system-config.index','admin/system-config*','bi-gear','messages.system_config'],
                ];
            @endphp
            @foreach($items as $it)
                @php list($route,$pat,$icon,$key) = $it; $active = $pat === 'admin/dashboard*' ? request()->is('admin/dashboard') : request()->is($pat); @endphp
                <a href="{{ route($route) }}" class="asc-item @if($active) active @endif"><i class="bi {{ $icon }}"></i> {{ __($key) }}</a>
            @endforeach
            <div class="asc-sec"></div>
            <a href="{{ route('home') }}" class="asc-item"><i class="bi bi-arrow-left"></i> {{ __("messages.back_to_store") }}</a>
            <a href="{{ route('logout') }}" class="asc-item"><i class="bi bi-box-arrow-right"></i> {{ __("messages.logout") }}</a>
        </nav>
    </aside>
    <main class="asc-main">
        <div class="asc-crumb"><a href="{{ route('admin.dashboard') }}">{{ __("messages.dashboard") }}</a> @yield('crumb','›')</div>
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
