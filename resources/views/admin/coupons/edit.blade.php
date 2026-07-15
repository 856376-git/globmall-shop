
@extends('layouts.admin')

@section('title', __('messages.edit'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __('messages.edit') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.code') }} *</label><input type="text" name="code" class="form-control" required value="{{ $coupon->code }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.type') }} *</label><select name="type" class="form-select" required><option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_off') }}</option><option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>{{ __('messages.percent_off') }}</option></select></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.value') }} *</label><input type="number" step="0.01" name="value" class="form-control" required value="{{ $coupon->value }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.min_order') }}</label><input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ $coupon->min_order_amount ?? 0 }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.usage') }}</label><input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.expires') }}</label><input type="datetime-local" name="expires_at" class="form-control" value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '' }}"></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="is_active" class="form-select"><option value="1" {{ $coupon->is_active ? 'selected' : '' }}>{{ __('messages.active') }}</option><option value="0" {{ !$coupon->is_active ? 'selected' : '' }}>{{ __('messages.inactive') }}</option></select></div>
</div>
<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form></div></div>
@endsection
