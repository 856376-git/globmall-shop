
@extends('layouts.admin')

@section('title', __('messages.coupons'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-ticket-perforated"></i> {{ __('messages.coupons') }}</h4>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> {{ __('messages.new_coupon') }}</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>{{ __('messages.code') }}</th><th>{{ __('messages.type') }}</th><th>{{ __('messages.value') }}</th><th>{{ __('messages.min_order') }}</th><th>{{ __('messages.usage') }}</th><th>{{ __('messages.expires') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.actions') }}</th></tr>
            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                <tr>
                    <td><code class="fs-6">{{ $coupon->code }}</code></td>
                    <td><span class="badge bg-{{ $coupon->type == 'fixed' ? 'info' : 'success' }}">{{ $coupon->type == 'fixed' ? __('messages.fixed_off') : __('messages.percent_off') }}</span></td>
                    <td class="fw-semibold">{{ $coupon->type == 'fixed' ? '$' : '' }}{{ $coupon->value }}{{ $coupon->type == 'percent' ? '%' : '' }}</td>
                    <td>${{ number_format($coupon->min_order_amount, 2) }}</td>
                    <td>{{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}</td>
                    <td>{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : __('messages.no_expiry') }}</td>
                    <td><span class="badge bg-{{ $coupon->isValid() ? 'success' : 'secondary' }}">{{ $coupon->isValid() ? __('messages.valid') : __('messages.invalid') }}</span></td>
                    <td>
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $coupons->links() }}
@endsection
