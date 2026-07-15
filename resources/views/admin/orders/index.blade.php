
@extends('layouts.admin')

@section('title', __('messages.order_management'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-receipt"></i> {{ __('messages.order_management') }}</h4>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3"><input type="text" name="order_no" class="form-control form-control-sm" placeholder="{{ __('messages.order_no') }}" value="{{ request('order_no') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('messages.processing') }}</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>{{ __('messages.shipped') }}</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('messages.delivered') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>{{ __('messages.refunded') }}</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm">{{ __('messages.filter') }}</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>{{ __('messages.order_no') }}</th><th>{{ __('messages.customer') }}</th><th>{{ __('messages.total') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.date') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_no }}</strong></td>
                    <td>{{ $order->user->name }}</td>
                    <td>${{ number_format($order->total, 2) }}</td>
                    <td><span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">{{ __('messages.'.$order->status) }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $orders->links() }}
@endsection
