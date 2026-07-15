@extends('layouts.app')

@section('title', __('messages.cart'))

@push('styles')
<style>
    .cart-item { display: flex; align-items: center; gap: 16px; padding: 16px; border: 1px solid #f0f0f0; border-radius: 12px; margin-bottom: 12px; transition: all .2s; background: #fff; }
    .cart-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .cart-item-img { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; }
    .cart-item-info { flex-grow: 1; }
    .cart-item-title { font-weight: 600; color: #333; text-decoration: none; }
    .cart-item-title:hover { color: #667eea; }
    .cart-item-sku { font-size: 0.8rem; color: #999; }
    .qty-control { display: inline-flex; align-items: center; border: 2px solid #f0f0f0; border-radius: 10px; overflow: hidden; }
    .qty-control button { width: 36px; height: 36px; border: none; background: #fff; font-weight: 700; font-size: 1.2rem; color: #666; transition: all .2s; }
    .qty-control button:hover { background: #f8f9fa; color: #667eea; }
    .qty-control input { width: 50px; text-align: center; border: none; font-weight: 600; padding: 4px; }
    .cart-summary { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); position: sticky; top: 100px; }
    .cart-summary .summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .cart-summary .summary-row:last-child { border-bottom: none; }
    .cart-summary .total { font-size: 1.3rem; font-weight: 800; color: #e74c3c; }
    .btn-checkout { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: #fff; border: none; border-radius: 12px; padding: 14px; font-weight: 700; width: 100%; transition: all .3s; }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(238,90,36,0.3); }
    .btn-continue { border: 2px solid #667eea; color: #667eea; border-radius: 12px; padding: 12px; font-weight: 600; width: 100%; text-align: center; text-decoration: none; display: block; transition: all .2s; }
    .btn-continue:hover { background: #667eea; color: #fff; }
    .empty-cart { text-align: center; padding: 60px 20px; }
    .empty-cart i { font-size: 5rem; color: #ddd; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-cart3 text-primary"></i> {{ __("messages.shopping_cart") }}</h3>

    @if($items->count())
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            @foreach($items as $item)
            <div class="cart-item">
                @php $cartImg = $item->product->primaryImage?->image ?? $item->product->images->first()?->image; $cartIsExt = $cartImg && str_starts_with($cartImg, 'http'); @endphp
                @if($cartImg)
                <img src="{{ $cartIsExt ? $cartImg : asset($cartImg) }}" alt="" class="cart-item-img">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center cart-item-img"><i class="bi bi-image text-muted"></i></div>
                @endif
                <div class="cart-item-info">
                    <a href="{{ route('product.show', $item->product->slug) }}" class="cart-item-title">{{ $item->product->name }}</a>
                    @if($item->variant)<div class="cart-item-sku">SKU: {{ $item->variant->sku }}</div>@endif
                    <div class="mt-2 fw-bold text-primary">${{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="qty-control">
                        <button type="button" onclick="updateQty({{ $item->id }}, -1)">−</button>
                        <input type="number" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" readonly>
                        <button type="button" onclick="updateQty({{ $item->id }}, 1)">+</button>
                    </div>
                    <div class="fw-bold fs-5">${{ number_format($item->subtotal, 2) }}</div>
                    <form action="{{ route('cart.remove') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" style="width:32px;height:32px;"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="col-lg-4">
            <div class="cart-summary">
                <h5 class="fw-bold mb-3">{{ __("messages.order_summary") }}</h5>
                <div class="summary-row">
                    <span class="text-muted">{{ __("messages.subtotal") }}</span>
                    <span class="fw-semibold">${{ number_format($total, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="text-muted">{{ __("messages.shipping") }}</span>
                    <span class="text-success">{{ $total >= 50 ? '{{ __('messages.free') }}' : '$5.99' }}</span>
                </div>
                <div class="summary-row">
                    <span class="text-muted">{{ __("messages.tax") }} (10%)</span>
                    <span>${{ number_format($total * 0.1, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="fw-bold">{{ __("messages.total") }}</span>
                    <span class="total">${{ number_format($total + ($total >= 50 ? 0 : 5.99) + $total * 0.1, 2) }}</span>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-ticket-perforated"></i> {{ __("messages.coupon_code") }}</label>
                    <div class="input-group">
                        <input type="text" name="coupon_code" id="couponCode" class="form-control" placeholder="{{ __("messages.enter_coupon_code") }}">
                        <button class="btn btn-outline-primary" type="button" onclick="applyCoupon()"><i class="bi bi-check-lg"></i></button>
                    </div>
                    <div id="couponMsg" class="small mt-1"></div>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-checkout mt-2"><i class="bi bi-credit-card"></i> {{ __("messages.proceed_to_checkout") }}</a>
                <a href="{{ route('shop') }}" class="btn-continue mt-3">{{ __("messages.continue_shopping") }}</a>
            </div>
        </div>
    </div>

    <script>
    function updateQty(itemId, delta) {
        const input = document.getElementById('qty-' + itemId);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        fetch('{{ route("cart.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ item_id: itemId, quantity: val })
        }).then(() => location.reload());
    }
    </script>
    @else
    <div class="empty-cart">
        <i class="bi bi-cart-x"></i>
        <h4 class="mt-3 fw-bold">{{ __("messages.empty_cart") }}</h4>
        <p class="text-muted">{{ __("messages.empty_cart_desc") }}</p>
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg mt-3">{{ __("messages.start_shopping") }}</a>
    </div>
    @endif
</div>
@endsection