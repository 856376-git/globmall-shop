@extends('layouts.app')
@section('title', __('messages.home'))
@section('content')

@if($banners->count())
<section class="amz-hero" id="top">
    <div class="amz-hero-track">
        @foreach($banners as $banner)
            @php $imgUrl = $banner->primaryImage?->image ?? $banner->images->first()?->image; @endphp
        <div class="amz-hero-slide @if($loop->first) active @endif" @if($imgUrl) style="background-image:linear-gradient(90deg,rgba(35,47,62,.85),rgba(35,47,62,.1)),url('{{ asset($imgUrl) }}')" @endif>
            <div class="amz-hero-cap">
                <h2>{{ __('messages.hero_offers') }}</h2>
                <p>{{ __('messages.hero_subtitle') }}</p>
                <a href="{{ route('shop') }}" class="btn-amz-yellow">{{ __('messages.shop_now') }}</a>
            </div>
        </div>
        @endforeach
    </div>
    @if($banners->count() > 1)
    <div class="amz-hero-dots">
        @foreach($banners as $b)<span @if($loop->first) class="active" @endif></span>@endforeach
    </div>
    @endif
</section>
@else
<div id="top"></div>
@endif

<div class="container py-3">

    @if($categories->count())
    <div class="row g-3 mb-3">
        @foreach($categories->take(4) as $cat)
        <div class="col-6 col-lg-3">
            <div class="amz-tile-card h-100">
                <h3>{{ $cat->name }}</h3>
                @php
                    $ps = $cat->products->take(4);
                @endphp
                <div class="amz-tile-grid">
                    @foreach($ps as $p)
                        @php $i = $p->primaryImage?->image ?? $p->images->first()?->image; @endphp
                        <div><img src="{{ $i ? asset($i) : '' }}" alt="{{ $p->name }}" @if(!$i) style="display:none" @endif></div>
                    @endforeach
                </div>
                @if($ps->isEmpty())
                <a href="{{ route('category', $cat->slug) }}"><div class="amz-tile-img d-flex align-items-center justify-content-center"><i class="bi bi-grid" style="font-size:2rem;color:#ccc;"></i></div></a>
                @endif
                <a href="{{ route('category', $cat->slug) }}" class="amz-tile-link">{{ __('messages.shop_now') }}</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($flashSaleProducts->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title"><i class="bi bi-lightning-charge-fill text-danger"></i> {{ __('messages.lightning_deals') }}</h2>
                <a href="{{ route('shop') }}" class="amz-sec-link">{{ __('messages.see_all') }}</a>
            </div>
            <div class="row g-2">
                @foreach($flashSaleProducts->take(6) as $product)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="amz-ld-card">
                        @php $imgUrl = $product->primaryImage?->image ?? $product->images->first()?->image; @endphp
                        @if($imgUrl)<img src="{{ asset($imgUrl) }}" class="ld-img" alt="{{ $product->name }}">@endif
                        <div class="ld-body">
                            <div class="ld-title"><a href="{{ route('product.show', $product->slug) }}">{{ Str::limit($product->name, 60) }}</a></div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="ld-deal-badge">{{ __('messages.deal') }}</span>
                                <span class="ld-price">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="amz-ld-bar"><div style="width:{{ rand(40,85) }}%"></div></div>
                            <small class="ld-claim">{{ __('messages.claimed', ['n' => rand(20,80)]) }}</small>
                            @if($product->compare_price)
                            <small class="text-muted ms-2">{{ __('messages.list_price') }}: <span class="text-decoration-line-through">${{ number_format($product->compare_price, 2) }}</span></small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($featuredProducts->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.featured_products') }}</h2>
                <a href="{{ route('shop') }}" class="amz-sec-link">{{ __('messages.see_all') }}</a>
            </div>
            <div class="amz-row" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
                @foreach($featuredProducts as $product)
                <div class="amz-col">@include('partials.product-card', ['product' => $product])</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($newProducts->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.new_arrivals') }}</h2>
                <a href="{{ route('shop') }}" class="amz-sec-link">{{ __('messages.see_all') }}</a>
            </div>
            <div class="amz-row" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
                @foreach($newProducts as $product)
                <div class="amz-col">@include('partials.product-card', ['product' => $product])</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($bestSellers->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.best_sellers') }}</h2>
                <a href="{{ route('shop') }}" class="amz-sec-link">{{ __('messages.see_all') }}</a>
            </div>
            <div class="amz-row" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
                @foreach($bestSellers as $product)
                <div class="amz-col">@include('partials.product-card', ['product' => $product])</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($coupons->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.coupons_deals') }}</h2>
            </div>
            <div class="row g-2">
                @foreach($coupons as $coupon)
                <div class="col-6 col-md-3">
                    <div class="d-flex border rounded p-2 align-items-center" style="border-style:dashed!important; border-color:#ccc;">
                        <div class="text-danger fw-bold me-2" style="font-size:1.1rem;">{{ $coupon->type == 'percentage' ? $coupon->value.'%' : '$'.$coupon->value }}</div>
                        <div class="small">
                            <div>{{ $coupon->name }}</div>
                            <small class="text-muted">{{ __('messages.min_order') }}: ${{ $coupon->min_order }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($brands->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.top_brands') }}</h2>
            </div>
            <div class="d-flex flex-wrap gap-3">
                @foreach($brands as $brand)
                <span class="border rounded px-3 py-2 bg-white text-muted fw-semibold">{{ $brand->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($reviews->count())
    <div class="amz-section">
        <div class="amz-sec-card">
            <div class="amz-sec-head">
                <h2 class="amz-sec-title">{{ __('messages.customer_reviews') }}</h2>
            </div>
            <div class="row g-2">
                @foreach($reviews as $review)
                <div class="col-md-4">
                    <div class="border rounded p-2 h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-weight:700;font-size:.8rem;">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="fw-semibold small">{{ $review->user->name ?? 'Customer' }}</div>
                                <div class="text-warning" style="font-size:.72rem;">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$review->rating?'-fill':'' }}"></i>@endfor
                                </div>
                            </div>
                        </div>
                        <p class="small text-muted mb-0" style="line-height:1.4;">{{ Str::limit($review->content, 120) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function(){
    const slides = document.querySelectorAll('.amz-hero-slide');
    const dots = document.querySelectorAll('.amz-hero-dots span');
    if (slides.length < 2) return;
    let cur = 0;
    function show(i){
        slides[cur].classList.remove('active');
        if (dots[cur]) dots[cur].classList.remove('active');
        cur = (i + slides.length) % slides.length;
        slides[cur].classList.add('active');
        if (dots[cur]) dots[cur].classList.add('active');
    }
    dots.forEach((d,i)=>d.addEventListener('click', ()=>show(i)));
    setInterval(()=>show(cur+1), 5000);
})();
</script>
@endpush