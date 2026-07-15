@extends('layouts.app')

@section('title', __('messages.order_no') . ' ' . $order->order_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-receipt"></i> {{ __('messages.order_no') }} {{ $order->order_no }}</h3>
    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }} fs-6">{{ __('messages.'.$order->status) }}</span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold">{{ __('messages.order_items') }}</h6>
                <table class="table table-sm">
                    <thead class="table-light"><tr><th>{{ __('messages.product') }}</th><th>{{ __('messages.sku') }}</th><th>{{ __('messages.price') }}</th><th>{{ __('messages.qty') }}</th><th>{{ __('messages.subtotal') }}</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td><code>{{ $item->sku }}</code></td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-end">
                    <div class="mb-1">{{ __('messages.subtotal') }}: <strong>${{ number_format($order->subtotal, 2) }}</strong></div>
                    <div class="mb-1">{{ __('messages.shipping') }}: <strong>${{ number_format($order->shipping_fee, 2) }}</strong></div>
                    <div class="mb-1">{{ __('messages.tax') }}: <strong>${{ number_format($order->tax, 2) }}</strong></div>
                    @if($order->discount > 0)<div class="mb-1">{{ __('messages.discount') }}: <strong class="text-danger">-${{ number_format($order->discount, 2) }}</strong></div>@endif
                    <hr>
                    <div class="fw-bold fs-5">{{ __('messages.total') }}: <span class="text-primary">${{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        @if($order->statusLogs->count())
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-clock-history"></i> {{ __('messages.order_status_timeline') }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($order->statusLogs as $log)
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $log->status == 'delivered' ? 'success' : ($log->status == 'cancelled' ? 'danger' : ($log->status == 'paid' ? 'info' : 'warning')) }}">{{ __('messages.'.$log->status) }}</span>
                        <small class="text-muted ms-1">{{ $log->created_at->format('M d') }}</small>
                        @if(!$loop->last)<i class="bi bi-arrow-right text-muted ms-1"></i>@endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold">{{ __('messages.shipping_address') }}</h6>
                @if($order->address)
                <p class="mb-0">{{ $order->address->first_name }} {{ $order->address->last_name }}<br>{{ $order->address->address_line1 }}<br>{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zipcode }}</p>
                @else
                <p class="text-muted">{{ __('messages.no_address_on_file') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('orders') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>
@endsection