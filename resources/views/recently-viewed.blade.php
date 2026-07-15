@extends('layouts.app')

@section('title', __('messages.recently_viewed'))

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-clock-history"></i> {{ __('messages.recently_viewed') }}</h2>
        <span class="text-muted">{{ $recentlyViewed->count() }} {{ __('messages.items') }}</span>
    </div>

    @if($recentlyViewed->count() == 0)
        <div class="text-center py-5">
            <i class="bi bi-clock" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">{{ __('messages.no_recently_viewed') }}</p>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">{{ __('messages.start_shopping') }}</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($recentlyViewed as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.product-card')
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection