
@extends('layouts.admin')

@section('title', __('messages.new_coupon'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-plus-lg"></i> {{ __('messages.new_coupon') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.coupons.store') }}" method="POST">
@csrf
<div class="row">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.code') }} *</label><input type="text" name="code" class="form-control" required></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.type') }} *</label><select name="type" class="form-select" required><option value="fixed">{{ __('messages.fixed_off') }}</option><option value="percent">{{ __('messages.percent_off') }}</option></select></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.value') }} *</label><input type="number" step="0.01" name="value" class="form-control" required></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.min_order') }}</label><input type="number" step="0.01" name="min_order_amount" class="form-control" value="0"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.usage') }}</label><input type="number" name="usage_limit" class="form-control" placeholder="0 = {{ __('messages.invalid') == 'invalid' ? '∞' : 'unlimited' }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.expires') }}</label><input type="datetime-local" name="expires_at" class="form-control"></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="is_active" class="form-select"><option value="1">{{ __('messages.active') }}</option><option value="0">{{ __('messages.inactive') }}</option></select></div>
</div>
<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form></div></div>
@endsection
