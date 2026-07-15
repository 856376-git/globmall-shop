<header class="amz-top sticky-top">
    <div class="amz-top-inner">
        <a class="amz-logo" href="{{ route('home') }}">
            <i class="bi bi-globe2"></i> GlobMall
        </a>
        <a class="amz-deliver d-none d-lg-flex" href="{{ route('account') }}">
            <i class="bi bi-geo-alt-fill"></i>
            <div>
                <div class="loc-s">{{ __('messages.deliver_to') }}</div>
                <div class="loc-c">{{ __('messages.country_label') }}</div>
            </div>
        </a>
        <div class="amz-search">
            <form action="{{ route('search') }}" method="GET">
                <select name="category" aria-label="category">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" @if(request('category')==$cat->id) selected @endif>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="search" name="q" id="searchInput" value="{{ request('q') }}" placeholder="{{ __('messages.search_amazon_placeholder') }}" autocomplete="off">
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
            <div id="searchDropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ccc; border-radius:4px; box-shadow:0 4px 8px rgba(0,0,0,.2); max-height:420px; overflow-y:auto; z-index:1050;"></div>
        </div>
        <div class="amz-tool dropdown d-none d-lg-flex">
            <div data-bs-toggle="dropdown" role="button">
                <span class="amz-flag">@if(app()->getLocale()=='en') EN @else CN @endif</span>
                <div>
                    <div class="t-s">{{ __('messages.language') }}</div>
                    <div class="t-l">{{ app()->getLocale()=='en' ? 'EN' : '中文' }}</div>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('en') }}">{{ __("messages.english") }}</a>
                <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('zh') }}">{{ __("messages.chinese") }}</a>
            </div>
        </div>
        @auth
        <div class="amz-tool dropdown d-none d-lg-flex">
            <div>
                <div class="t-s">{{ __('messages.hello') }}, {{ auth()->user()->name }}</div>
                <div class="t-l">{{ __('messages.account_lists') }}</div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('account') }}">{{ __('messages.my_account') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('orders') }}">{{ __('messages.my_orders') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('wishlist') }}">{{ __('messages.wishlist') }}</a></li>
                @if(auth()->user()->isAdmin())
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('messages.admin_panel') }}</a></li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li><form action="{{ route('logout') }}" method="POST">@csrf <button class="dropdown-item text-danger">{{ __('messages.logout') }}</button></form></li>
            </ul>
        </div>
        @else
        <div class="amz-tool dropdown d-none d-lg-flex">
            <div>
                <div class="t-s">{{ __('messages.hello_signin') }}</div>
                <div class="t-l">{{ __('messages.login') }}</div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('login') }}">{{ __('messages.login') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('register') }}">{{ __('messages.register') }}</a></li>
            </ul>
        </div>
        @endauth
        <a class="amz-cart" href="{{ route('cart') }}">
            <span class="c-count">@auth {{ auth()->user()->cartItems()->sum('quantity') }} @else 0 @endauth</span>
            <i class="bi bi-cart3"></i>
            <span class="c-l">{{ __('messages.cart') }}</span>
        </a>
    </div>
</header>
<nav class="amz-nav">
    <div class="amz-nav-inner">
        <a href="#" class="amz-burger" data-bs-toggle="offcanvas" data-bs-target="#amzSideMenu">
            <i class="bi bi-list"></i> {{ __('messages.all') }}
        </a>
        @php $navCats = is_iterable($categories ?? null) ? collect($categories)->take(8) : []; @endphp @foreach($navCats as $cat)
            <a href="{{ route('category', $cat->slug) }}">{{ $cat->name }}</a>
        @endforeach
        <a href="{{ route('shop') }}">{{ __('messages.deals') }}</a>
        <a href="{{ route('shop') }}">{{ __('messages.new_arrivals') }}</a>
    </div>
</nav>

@if(session('success'))
<div class="container mt-2"><div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
@endif
@if(session('error'))
<div class="container mt-2"><div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
@endif

<script>
(function() {
    const input = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchDropdown');
    if (!input || !dropdown) return;
    let t;
    input.addEventListener('input', function() {
        clearTimeout(t);
        const q = this.value.trim();
        if (q.length < 2) { dropdown.style.display = 'none'; return; }
        t = setTimeout(function() {
            fetch('/api/search/suggestions?q=' + encodeURIComponent(q) + '&locale={{ app()->getLocale() }}')
                .then(r => r.json()).then(data => {
                    if (!data.length) {
                        dropdown.innerHTML = '<div class="dropdown-item text-muted small">{{ __("messages.no_results_found") }}</div>';
                    } else {
                        dropdown.innerHTML = data.map(item => {
                            const img = item.image ? '<img src="' + item.image + '" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">' : '';
                            return '<a href="' + item.url + '" class="dropdown-item d-flex align-items-center py-2">' + img +
                                '<div class="flex-grow-1"><div class="small text-dark">' + item.name + '</div>' +
                                '<div class="text-danger small">$' + item.price + '</div></div></a>';
                        }).join('');
                    }
                    dropdown.style.display = 'block';
                }).catch(() => { dropdown.style.display = 'none'; });
        }, 300);
    });
    document.addEventListener('click', e => { if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none'; });
})();
</script>