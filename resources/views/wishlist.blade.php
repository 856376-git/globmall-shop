@extends('layouts.app')

@section('title', __('messages.my_wishlist'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-heart-fill text-danger"></i> {{ __('messages.my_wishlist') }}</h3>
    <span class="text-muted">{{ $items->total() }} {{ __('messages.items') }}</span>
</div>

@if($items->count())
<div class="row g-3">
    @foreach($items as $item)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card card-product h-100">
            @php
                $imgUrl = $item->product->primaryImage?->image ?? $item->product->images->first()?->image;
                $isExternal = $imgUrl && (str_starts_with($imgUrl, 'http://') || str_starts_with($imgUrl, 'https://'));
            @endphp
            <a href="{{ route('product.show', $item->product->slug) }}" class="text-decoration-none">
                @if($imgUrl)
                    <img src="{{ $isExternal ? $imgUrl : asset($imgUrl) }}" class="card-img-top" alt="{{ $item->product->name }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:220px;"><i class="bi bi-image text-muted" style="font-size:3rem;"></i></div>
                @endif
            </a>
            <div class="card-body d-flex flex-column">
                <h6 class="card-title"><a href="{{ route('product.show', $item->product->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($item->product->name, 35) }}</a></h6>
                <span class="price-tag">${{ number_format($item->product->price, 2) }}</span>
                <div class="mt-2 d-flex gap-1">
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-add-cart btn-sm w-100"><i class="bi bi-cart-plus"></i></button>
                    </form>
                    <form action="{{ route('wishlist.destroy', $item->product_id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
{{ $items->links() }}
@else
<div class="text-center py-5">
    <i class="bi bi-heart text-muted" style="font-size:5rem;"></i>
    <p class="mt-3 text-muted fs-5">{{ __('messages.wishlist_empty') }}</p>
    <a href="{{ route('shop') }}" class="btn btn-primary">{{ __('messages.continue_shopping') }}</a>
</div>
@endif
@endsection