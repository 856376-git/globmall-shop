
@extends('layouts.admin')

@section('title', __('messages.edit'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __('messages.edit') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold text-primary">{{ __("messages.english_label") }}</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="en[name]" class="form-control" required value="{{ $brand->translations->where('locale','en')->first()?->name }}"></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="en[description]" class="form-control" rows="2">{{ $brand->translations->where('locale','en')->first()?->description }}</textarea></div>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold text-danger">中文</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="zh[name]" class="form-control" required value="{{ $brand->translations->where('locale','zh')->first()?->name }}"></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="zh[description]" class="form-control" rows="2">{{ $brand->translations->where('locale','zh')->first()?->description }}</textarea></div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.slug') }} *</label><input type="text" name="slug" class="form-control" required value="{{ $brand->slug }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.image') }}</label>@if($brand->logo)<div class="mb-1"><img src="{{ asset($brand->logo) }}" style="max-height:60px;"></div>@endif<input type="file" name="logo" class="form-control" accept="image/*"></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.sort_order') }}</label><input type="number" name="sort_order" class="form-control" value="{{ $brand->sort_order ?? 0 }}"></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="status" class="form-select"><option value="1" {{ $brand->status ? 'selected' : '' }}>{{ __('messages.active') }}</option><option value="0" {{ !$brand->status ? 'selected' : '' }}>{{ __('messages.inactive') }}</option></select></div>
</div>
<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form></div></div>
@endsection
