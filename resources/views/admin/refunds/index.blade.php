
@extends('layouts.admin')

@section('title', __('messages.refund_requests'))

@section('content')
<h2 class="mb-4"><i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.refund_requests') }}</h2>

<table class="table table-bordered bg-white">
    <thead class="table-light">
        <tr>
            <th>{{ __('messages.refund_id') }}</th>
            <th>{{ __('messages.order_no') }}</th>
            <th>{{ __('messages.customer') }}</th>
            <th>{{ __('messages.type') }}</th>
            <th>{{ __('messages.amount') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th>{{ __('messages.date') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($refunds as $r)
        <tr>
            <td>{{ $r->id }}</td>
            <td><a href="{{ route('admin.orders.show', $r->order_id) }}">{{ $r->order_no }}</a></td>
            <td>{{ $r->user->name }}</td>
            <td><span class="badge bg-secondary">{{ ucfirst($r->type) }}</span></td>
            <td>${{ number_format($r->refund_amount ?? $r->order->total ?? 0, 2) }}</td>
            <td>
                @if($r->status === 'pending')
                    <span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                @elseif($r->status === 'approved')
                    <span class="badge bg-info">{{ __('messages.approved') }}</span>
                @elseif($r->status === 'rejected')
                    <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                @else
                    <span class="badge bg-success">{{ __('messages.completed') }}</span>
                @endif
            </td>
            <td>{{ $r->created_at->format('Y-m-d') }}</td>
            <td><a href="{{ route('admin.refunds.show', $r->id) }}" class="btn btn-sm btn-primary">{{ __('messages.view') }}</a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_refund_requests') }}</td></tr>
        @endforelse
    </tbody>
</table>
{{ $refunds->links() }}
@endsection
