
@extends('layouts.admin')

@section('title', __('messages.new_role') ?? 'New Role')

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-plus-lg"></i> {{ __('messages.new_role') ?? 'New Role' }}</h4>

<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.roles.store') }}" method="POST">
@csrf
<div class="row mb-3">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="name" class="form-control" required></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.slug') }} * <small class="text-muted">(lowercase, no spaces)</small></label><input type="text" name="slug" class="form-control" pattern="[a-z0-9\-]+" required></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.sort_order') }}</label><input type="number" name="sort_order" class="form-control" value="0"></div>
    <div class="col-md-12 mb-2"><label class="form-label">{{ __('messages.description') }}</label><input type="text" name="description" class="form-control"></div>
</div>

<h6 class="fw-bold mt-3 mb-2"><i class="bi bi-key"></i> {{ __('messages.permissions') ?? 'Permissions' }}</h6>
<div class="row">
    @foreach($permissions as $group => $perms)
    <div class="col-md-6 mb-3">
        <div class="border rounded p-3 h-100">
            <h6 class="fw-bold text-primary mb-2">{{ ucfirst($group) }}</h6>
            @foreach($perms as $perm)
            <div class="form-check">
                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="form-check-input" id="perm_{{ $perm->id }}">
                <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }} <small class="text-muted">({{ $perm->slug }})</small></label>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form>
</div></div>
@endsection
