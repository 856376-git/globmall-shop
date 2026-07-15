
@extends('layouts.admin')

@section('title', __('messages.edit'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __('messages.edit') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.categories.update', $category) }}" method="POST">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold text-primary">{{ __("messages.english_label") }}</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="en[name]" class="form-control" required value="{{ $category->translations->where('locale','en')->first()?->name }}"></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="en[description]" class="form-control" rows="2">{{ $category->translations->where('locale','en')->first()?->description }}</textarea></div>
        <div class="mb-2"><label class="form-label">{{ __("messages.meta_title") }}</label><input type="text" name="en[meta_title]" class="form-control" value="{{ $category->translations->where('locale','en')->first()?->meta_title }}"></div>
        <div class="mb-2"><label class="form-label">{{ __("messages.meta_description") }}</label><textarea name="en[meta_description]" class="form-control" rows="2">{{ $category->translations->where('locale','en')->first()?->meta_description }}</textarea></div>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold text-danger">中文</h6>
        <div class="mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="zh[name]" class="form-control" required value="{{ $category->translations->where('locale','zh')->first()?->name }}"></div>
        <div class="mb-2"><label class="form-label">{{ __('messages.description') }}</label><textarea name="zh[description]" class="form-control" rows="2">{{ $category->translations->where('locale','zh')->first()?->description }}</textarea></div>
        <div class="mb-2"><label class="form-label">Meta 标题</label><input type="text" name="zh[meta_title]" class="form-control" value="{{ $category->translations->where('locale','zh')->first()?->meta_title }}"></div>
        <div class="mb-2"><label class="form-label">Meta 描述</label><textarea name="zh[meta_description]" class="form-control" rows="2">{{ $category->translations->where('locale','zh')->first()?->meta_description }}</textarea></div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.slug') }} *</label><input type="text" name="slug" class="form-control" required value="{{ $category->slug }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.parent') }}</label><select name="parent_id" class="form-select"><option value="">{{ __('messages.parent') }}</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $category->parent_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.sort_order') }}</label><input type="number" name="sort_order" class="form-control" value="{{ $category->sort_order ?? 0 }}"></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="status" class="form-select"><option value="1" {{ $category->status ? 'selected' : '' }}>{{ __('messages.active') }}</option><option value="0" {{ !$category->status ? 'selected' : '' }}>{{ __('messages.inactive') }}</option></select></div>
</div>
<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form></div></div>
@endsection
