@extends('layouts.app')

@section('title', __('messages.order_placed'))

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
    </div>
    <h2 class="fw-bold">{{ __('messages.order_placed') }}</h2>
    <p class="text-muted mt-2">Order Number: <strong>{{ $order->order_no }}</strong></p>
    <p class="text-muted">Total: <strong class="text-primary">${{ number_format($order->total, 2) }}</strong></p>

    <div class="card shadow-sm mt-4 mx-auto" style="max-width:500px;">
        <div class="card-body text-start">
            <h6 class="fw-bold">{{ __("messages.order_items") }}</h6>
            @foreach($order->items as $item)
            <div class="d-flex justify-content-between mb-2">
                <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                <strong>${{ number_format($item->subtotal, 2) }}</strong>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        @if($order->status === 'pending')
        <a href="{{ route('stripe.checkout', $order->order_no) }}" class="btn btn-success btn-lg">
            <i class="bi bi-credit-card"></i> Pay Now with Stripe
        </a>
        @endif
        <a href="{{ route('orders') }}" class="btn btn-primary">{{ __('messages.my_orders') }}</a>
        <a href="{{ route('shop') }}" class="btn btn-outline-secondary ms-2">{{ __('messages.continue_shopping') }}</a>
    </div>
</div>
@endsection