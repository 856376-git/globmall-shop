
@extends('layouts.admin')

@section('title', __('messages.refund_requests') . ' #' . $refund->id)

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.refund_requests') }} #{{ $refund->id }}</h4>
<a href="{{ route('admin.refunds.index') }}" class="btn btn-link mb-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6 class="fw-bold">{{ __('messages.order_no') }}: <a href="{{ route('admin.orders.show', $refund->order_id) }}">{{ $refund->order_no }}</a></h6>
            <table class="table table-sm">
                <tr><th>{{ __('messages.type') }}:</th><td>{{ ucfirst($refund->type) }}</td></tr>
                <tr><th>{{ __('messages.amount') }}:</th><td>${{ number_format($refund->refund_amount ?? 0, 2) }}</td></tr>
                <tr><th>{{ __('messages.status') }}:</th><td>
                    @if($refund->status === 'pending')<span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                    @elseif($refund->status === 'approved')<span class="badge bg-info">{{ __('messages.approved') }}</span>
                    @elseif($refund->status === 'rejected')<span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                    @else<span class="badge bg-success">{{ __('messages.completed') }}</span>@endif
                </td></tr>
                <tr><th>{{ __('messages.date') }}:</th><td>{{ $refund->created_at->format('Y-m-d H:i') }}</td></tr>
                <tr><th>Reason:</th><td>{{ $refund->reason ?? '-' }}</td></tr>
                <tr><th>Admin Note:</th><td>{{ $refund->admin_note ?? '-' }}</td></tr>
            </table>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="fw-bold">{{ __('messages.customer') }}</h6>
            <p>{{ $refund->user->name }}<br>{{ $refund->user->email }}</p>
        </div></div>
    </div>
</div>
@endsection
