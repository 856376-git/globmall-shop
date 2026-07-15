<!-- Mobile Bottom Navigation -->
<nav class="crmb-bottom-nav d-lg-none">
    <div class="container">
        <div class="d-flex justify-content-around">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i>
                <span>{{ __("messages.home") }}</span>
            </a>
            <a href="{{ route('shop') }}" class="nav-item {{ request()->routeIs('shop') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>{{ __("messages.shop") }}</span>
            </a>
            <a href="{{ route('cart') }}" class="nav-item {{ request()->routeIs('cart') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i>
                <span>{{ __("messages.cart") }}</span>
            </a>
            @auth
            <a href="{{ route('account') }}" class="nav-item {{ request()->routeIs('account') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i>
                <span>{{ __("messages.my_account") }}</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i>
                <span>{{ __("messages.login") }}</span>
            </a>
            @endauth
        </div>
    </div>
</nav>