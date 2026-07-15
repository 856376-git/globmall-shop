
@extends('layouts.admin')

@section('title', __('messages.edit') . ' - ' . $role->name)

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __('messages.edit') }} - {{ $role->name }}</h4>

<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.roles.update', $role) }}" method="POST">
@csrf @method('PUT')
<div class="row mb-3">
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.name') }} *</label><input type="text" name="name" class="form-control" required value="{{ $role->name }}"></div>
    <div class="col-md-4 mb-2"><label class="form-label">{{ __('messages.slug') }} *</label><input type="text" name="slug" class="form-control" pattern="[a-z0-9\-]+" required value="{{ $role->slug }}" {{ $role->is_system ? 'readonly' : '' }}></div>
    <div class="col-md-2 mb-2"><label class="form-label">{{ __('messages.sort_order') }}</label><input type="number" name="sort_order" class="form-control" value="{{ $role->sort_order }}"></div>
    <div class="col-md-12 mb-2"><label class="form-label">{{ __('messages.description') }}</label><input type="text" name="description" class="form-control" value="{{ $role->description }}"></div>
</div>

@if($role->is_system)
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> {{ __('messages.system_role_warning') ?? 'This is a system role. Slug cannot be changed, but permissions can be adjusted.' }}</div>
@endif

<h6 class="fw-bold mt-3 mb-2"><i class="bi bi-key"></i> {{ __('messages.permissions') ?? 'Permissions' }}</h6>
<div class="row">
    @foreach($permissions as $group => $perms)
    <div class="col-md-6 mb-3">
        <div class="border rounded p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-primary mb-0">{{ ucfirst($group) }}</h6>
                <button type="button" class="btn btn-link btn-sm p-0" onclick="document.querySelectorAll('.group-{{ $group }}').forEach(cb => cb.checked = !cb.checked); return false;">{{ __('messages.toggle_all') ?? 'Toggle' }}</button>
            </div>
            @foreach($perms as $perm)
            <div class="form-check">
                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="form-check-input group-{{ $group }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, $assignedIds) ? 'checked' : '' }}>
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
