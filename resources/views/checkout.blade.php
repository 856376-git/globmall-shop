@extends('layouts.app')

@section('title', __('messages.checkout'))

@section('content')
<h3 class="mb-4"><i class="bi bi-credit-card"></i> {{ __('messages.checkout') }}</h3>

<form action="{{ route('checkout.place') }}" method="POST">
    @csrf
    <div class="row">
        <!-- 瀹革缚鏅堕敍姘勾閸р偓+鐠併垹宕熸径鍥ㄦ暈 -->
        <div class="col-md-8">
            <!-- 閺€鎯版彛閸︽澘娼?-->
            <div class="checkout-step shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt"></i> {{ __("messages.shipping_address") }}</h5>
                @if($addresses->count())
                    @foreach($addresses as $addr)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="address_id" id="addr{{ $addr->id }}" value="{{ $addr->id }}" {{ $addr->is_default ? 'checked' : '' }} required>
                        <label class="form-check-label" for="addr{{ $addr->id }}">
                            <strong>{{ $addr->first_name }} {{ $addr->last_name }}</strong>
                            <span class="text-muted">- {{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }} {{ $addr->zipcode }}, {{ $addr->country }}</span>
                            @if($addr->is_default)<span class="badge bg-primary ms-1">{{ __("messages.default") }}</span>@endif
                        </label>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">Please <a href="{{ route('addresses') }}">add a shipping address</a> {{ __("messages.add_address_first") }}</div>
                @endif
            </div>

            <!-- 娴兼ɑ鍎崚?-->
            <div class="checkout-step shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated"></i> {{ __('messages.apply_coupon') }}</h5>
                <div class="input-group">
                    <input type="text" name="coupon_code" class="form-control" placeholder="{{ __("messages.enter_coupon_code") }}">
                    <button class="btn btn-outline-primary" type="button">{{ __('messages.apply_coupon') }}</button>
                </div>
            </div>

            <!-- 婢跺洦鏁?-->
            <div class="checkout-step shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __("messages.order_note") }}</h5>
                <textarea name="note" class="form-control" rows="3" placeholder="{{ __("messages.order_note_placeholder") }}"></textarea>
            </div>
        </div>

        <!-- 閸欏厖鏅堕敍姘愁吂閸楁洘鎲崇憰?-->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">{{ __("messages.order_summary") }}</h5>
                    @foreach($items as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ Str::limit($item->product->name, 25) }} x{{ $item->quantity }}</span>
                        <strong>${{ number_format($item->subtotal, 2) }}</strong>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('messages.subtotal') }}</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('messages.shipping') }}</span>
                        <span>${{ number_format($shippingFee, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __("messages.tax") }}</span>
                        <span>${{ number_format($tax, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>{{ __('messages.total') }}</span>
                        <span class="text-primary">${{ number_format($total, 2) }}</span>
                    </div>

                    @if($addresses->count())
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                        <i class="bi bi-lock"></i> {{ __('messages.place_order') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
@endsection