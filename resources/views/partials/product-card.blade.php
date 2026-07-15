<div class="amz-pcard h-100">
    @if($product->compare_price)
        <span class="pc-badge">-{{ $product->discount_percent }}%</span>
    @endif
    <a href="{{ route('product.show', $product->slug) }}" class="pc-img">
        @php
            $imgUrl = $product->primaryImage?->image ?? $product->images->first()?->image;
            $isExternal = $imgUrl && (str_starts_with($imgUrl, 'http://') || str_starts_with($imgUrl, 'https://'));
        @endphp
        @if($imgUrl)
            <img src="{{ $isExternal ? $imgUrl : asset($imgUrl) }}" alt="{{ $product->name }}">
        @else
            <i class="bi bi-image pc-noimg"></i>
        @endif
    </a>
    <div class="pc-body">
        <div class="pc-cat">{{ $product->category->name ?? '' }}</div>
        <div class="pc-title"><a href="{{ route('product.show', $product->slug) }}">{{ Str::limit($product->name, 80) }}</a></div>
        @if($product->review_count > 0)
        <div class="pc-stars">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= round($product->average_rating))<i class="bi bi-star-fill"></i>@else<i class="bi bi-star"></i>@endif
            @endfor
            <span>({{ $product->review_count }})</span>
        </div>
        @endif
        <div class="pc-price">
            <span class="pc-now">${{ number_format($product->price, 2) }}</span>
            @if($product->compare_price)
                <span class="pc-was">${{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>
        <div class="pc-prime">{{ __('messages.prime_badge') }}</div>
        <div class="pc-add">
            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"><i class="bi bi-cart-plus"></i> {{ __('messages.add_to_cart') }}</button>
                </form>
            @else
                <button disabled>{{ __('messages.out_of_stock') }}</button>
            @endif
        </div>
    </div>
</div>