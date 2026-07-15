
@extends('layouts.admin')

@section('title', __('messages.new_product'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-plus-lg"></i> {{ __('messages.new_product') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold text-primary">{{ __("messages.english_label") }}</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="en[name]" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="en[description]" class="form-control" rows="3"></textarea></div>
        <div class="mb-2"><label class="form-label">{{ __("messages.meta_title") }}</label><input type="text" name="en[meta_title]" class="form-control"></div>
        <div class="mb-2"><label class="form-label">{{ __("messages.meta_description") }}</label><textarea name="en[meta_description]" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold text-danger">中文</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="zh[name]" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="zh[description]" class="form-control" rows="3"></textarea></div>
        <div class="mb-2"><label class="form-label">Meta 标题</label><input type="text" name="zh[meta_title]" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Meta 描述</label><textarea name="zh[meta_description]" class="form-control" rows="2"></textarea></div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-3 mb-2"><label class="form-label">SKU *</label><input type="text" name="sku" class="form-control" required></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.category') }} *</label><select name="category_id" class="form-select" required>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.brands') }}</label><select name="brand_id" class="form-select"><option value="">{{ __('messages.brands') }}</option>@foreach($brands ?? [] as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach</select></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.price') }} *</label><input type="number" step="0.01" name="price" class="form-control" required></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.stock') }}</label><input type="number" name="stock" class="form-control" value="0"></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.image') }}</label><input type="file" name="images[]" class="form-control" accept="image/*" multiple></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="status" class="form-select"><option value="1">{{ __('messages.active') }}</option><option value="0">{{ __('messages.inactive') }}</option></select></div>
</div>
<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form></div></div>
@endsection
