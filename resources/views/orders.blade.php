@extends('layouts.app')

@section('title', __('messages.my_orders'))

@section('content')
<h3 class="mb-4"><i class="bi bi-receipt"></i> {{ __('messages.my_orders') }}</h3>

@if($orders->count())
<div class="table-responsive">
    <table class="table table-hover shadow-sm bg-white rounded">
        <thead class="table-light">
            <tr>
                <th>{{ __('messages.order_no') }}</th>
                <th>{{ __('messages.order_date') }}</th>
                <th>{{ __('messages.total') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td><strong>{{ $order->order_no }}</strong></td>
                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                <td class="fw-semibold">${{ number_format($order->total, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : ($order->status == 'paid' ? 'info' : 'warning')) }}">
                        {{ __('messages.'.$order->status) }}
                    </span>
                </td>
                <td><a href="{{ route('order.detail', $order->order_no) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.view') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $orders->links() }}
@else
<div class="text-center py-5">
    <i class="bi bi-receipt text-muted" style="font-size:4rem;"></i>
    <p class="mt-3 text-muted">{{ __('messages.no_orders_yet') }}</p>
    <a href="{{ route('shop') }}" class="btn btn-primary">{{ __('messages.continue_shopping') }}</a>
</div>
@endif
@endsection