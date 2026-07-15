@extends("layouts.app")
@section("title", $product->name)
@section("content")
<div class="amz-pd">
<div class="container">
    <nav class="small py-2">
        <a href="{{ route('home') }}">{{ __('messages.home') }}</a> ›
        <a href="{{ route('shop') }}">{{ __('messages.shop') }}</a> ›
        @if($product->category)<a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a> ›@endif
        <span class="text-muted">{{ Str::limit($product->name, 50) }}</span>
    </nav>
    <div class="amz-pd-main">
        <div class="row g-4">
            {{-- Gallery --}}
            <div class="col-lg-5">
                @if($product->images->count())
                    @php $mainImg = $product->images->first()->image; $isExt = str_starts_with($mainImg, 'http'); @endphp
                    <div class="d-flex gap-2">
                        @if($product->images->count() > 1)
                        <div class="amz-pd-thumbs d-flex flex-column me-1">
                            @foreach($product->images as $img)
                            @php $i = $img->image; $e = str_starts_with($i, 'http'); @endphp
                            <img src="{{ $e ? $i : asset($i) }}" class="{{ $loop->first ? 'active' : '' }}" onclick="document.getElementById('pdMain').src=this.src; document.querySelectorAll('.amz-pd-thumbs img').forEach(x=>x.classList.remove('active')); this.classList.add('active');">
                            @endforeach
                        </div>
                        @endif
                        <div class="flex-grow-1 text-center">
                            <img id="pdMain" src="{{ $isExt ? $mainImg : asset($mainImg) }}" alt="{{ $product->name }}" style="max-width:100%; max-height:480px; object-fit:contain;">
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center" style="height:420px;"><i class="bi bi-image text-muted" style="font-size:5rem;"></i></div>
                @endif
            </div>
            {{-- Info --}}
            <div class="col-lg-4">
                <h1 class="amz-pd-title">{{ $product->name }}</h1>
                <div class="amz-pd-stars mb-2">
                    @if($product->review_count > 0)
                        @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=round($product->average_rating) ? '-fill' : '' }}"></i>@endfor
                        <a href="#reviews" class="ms-2 small">{{ $product->review_count }} {{ __('messages.ratings') }}</a>
                    @else
                        <span class="small text-muted">{{ __('messages.no_reviews') }}</span>
                    @endif
                </div>
                <hr>
                <div class="mb-2">
                    @if($product->compare_price)
                        <span class="amz-pd-price-main">${{ number_format($product->price, 2) }}</span>
                        <span class="text-muted text-decoration-line-through ms-2">${{ number_format($product->compare_price, 2) }}</span>
                        <span class="badge bg-danger ms-1">-{{ $product->discount_percent }}%</span>
                    @else
                        <span class="amz-pd-price-main">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                @if($product->brand)
                    <div class="small mb-2"><span class="text-muted">{{ __('messages.brand') }}:</span> {{ $product->brand->name }}</div>
                @endif
                @if($product->stock > 0)
                    <div class="small text-success mb-2">{{ __('messages.in_stock') }}</div>
                @else
                    <div class="small text-danger mb-2">{{ __('messages.out_of_stock') }}</div>
                @endif
                <div class="small text-muted">{{ __('messages.shipping') }}: {{ __('messages.free_shipping') }}</div>
                <div class="small text-muted">{{ __('messages.sold_by') }}: GlobMall</div>
                @if($product->description)
                    <hr>
                    <h6 class="fw-bold">{{ __('messages.description') }}</h6>
                    <div class="small" style="line-height:1.5;">{!! nl2br(e($product->description)) !!}</div>
                @endif
            </div>
            {{-- Buy box --}}
            <div class="col-lg-3">
                <div class="amz-pd-buybox">
                    <div class="bb-price mb-2">${{ number_format($product->price, 2) }}</div>
                    @if($product->compare_price)
                        <div class="small text-muted mb-2"><span class="text-decoration-line-through">${{ number_format($product->compare_price, 2) }}</span></div>
                    @endif
                    <div class="small text-success mb-3">{{ __('messages.free_delivery') }}</div>
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="small">{{ __('messages.qty') }}:</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control form-control-sm" style="width:70px;">
                            </div>
                            <button type="submit" class="bb-btn"><i class="bi bi-cart-plus"></i> {{ __('messages.add_to_cart') }}</button>
                        </form>
                    @else
                        <button class="bb-btn" disabled>{{ __('messages.out_of_stock') }}</button>
                    @endif
                    @auth
                    <form action="{{ route('wishlist.toggle') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @php $inWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists(); @endphp
                        <button type="submit" class="bb-btn" style="background:#fff; border:1px solid var(--amz-border); color:var(--amz-text);">
                            <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }}"></i> {{ __('messages.wishlist') }}
                        </button>
                    </form>
                    @endauth
                    <div class="mt-3 small">
                        <div><i class="bi bi-truck"></i> {{ __('messages.free_returns') }}</div>
                        <div><i class="bi bi-shield-check"></i> {{ __('messages.secure_payment') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    <div id="reviews" class="amz-pd-main mt-3">
        <h2 class="amz-sec-title mb-3">{{ __('messages.customer_reviews') }}</h2>
        @if($reviews->count())
        <div class="row g-3">
            @foreach($reviews as $review)
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-weight:700;">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div class="fw-semibold small">{{ $review->user->name ?? 'Customer' }}</div>
                            <div class="text-warning" style="font-size:.72rem;">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$review->rating?'-fill':'' }}"></i>@endfor
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mb-1" style="line-height:1.5;">{{ $review->content }}</p>
                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted">{{ __('messages.no_reviews') }}</p>
        @endif
    </div>

    {{-- Related products --}}
    @if(isset($related) && $related->count())
    <div class="amz-pd-main mt-3">
        <h2 class="amz-sec-title mb-3">{{ __('messages.related_products') }}</h2>
        <div class="amz-row" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
            @foreach($related as $rp)
            <div class="amz-col">@include('partials.product-card', ['product' => $rp])</div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</div>
@endsection