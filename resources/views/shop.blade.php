@extends('layouts.app')
@section('title', __('messages.shop'))
@section('content')
<div class="container-fluid py-3 px-0" style="max-width:1500px; margin:0 auto;">
    <div class="row g-3 m-0">
        {{-- Filters sidebar --}}
        <div class="col-lg-2 p-0 ps-lg-2">
            <div class="amz-shop-side">
                <div class="amz-side-sec">
                    <h6>{{ __("messages.filters") }}</h6>
                    <form method="GET" action="{{ route('shop') }}">
                        @isset($keyword)<input type="hidden" name="q" value="{{ $keyword }}">@endisset
                        <label class="form-label small fw-bold mb-1">{{ __("messages.category") }}</label>
                        @foreach($categories as $cat)
                        <label><input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }} onchange="this.form.submit()"> {{ $cat->name }}</label>
                        @endforeach
                        <div class="mt-3"></div>
                        <label class="form-label small fw-bold mb-1">{{ __("messages.price_range") }}</label>
                        <input type="number" name="min_price" class="form-control form-control-sm mb-1" placeholder="{{ __("messages.min") }}" value="{{ request('min_price') }}">
                        <input type="number" name="max_price" class="form-control form-control-sm mb-2" placeholder="{{ __("messages.max") }}" value="{{ request('max_price') }}">
                        <label class="form-label small fw-bold mb-1">{{ __("messages.sort_by") }}</label>
                        <select name="sort" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
                            <option value="">{{ __("messages.default") }}</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __("messages.price_low_high") }}</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __("messages.price_high_low") }}</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __("messages.newest_first") }}</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-warning w-100">{{ __("messages.apply_filters") }}</button>
                        <a href="{{ route('shop') }}" class="btn btn-sm btn-link w-100 mt-1">{{ __("messages.reset_all") }}</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Products list --}}
        <div class="col-lg-10 p-0 pe-lg-2">
            <div class="amz-shop-main">
                <div class="amz-shop-result-head">
                    <h2>@isset($keyword){{ __("messages.search_results") }}: "{{ $keyword }}" @else {{ __("messages.shop_all_products") }} @endisset</h2>
                    <span class="text-muted small">{{ $products->total() }} {{ __("messages.results") }}</span>
                </div>
                @if($products->count())
                <div class="amz-shop-list">
                    @foreach($products as $product)
                    <div>@include('partials.product-card', ['product' => $product])</div>
                    @endforeach
                </div>
                <div class="mt-3">{{ $products->withQueryString()->links() }}</div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size:4rem;"></i>
                    <h5 class="mt-3 fw-bold">{{ __("messages.no_products_found") }}</h5>
                    <p class="text-muted">{{ __("messages.try_adjusting_filters") }}</p>
                    <a href="{{ route('shop') }}" class="btn btn-warning">{{ __("messages.clear_filters") }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection