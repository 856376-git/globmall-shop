
@extends('layouts.admin')

@section('title', __('messages.edit') . ' - ' . $user->name)

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-pencil"></i> {{ __('messages.edit') }} - {{ $user->name }}</h4>
<a href="{{ route('admin.users.index') }}" class="btn btn-link mb-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>

<div class="card shadow-sm"><div class="card-body">
<form action="{{ route('admin.users.update', $user) }}" method="POST">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-6 mb-2"><label class="form-label">{{ __('messages.name') }}</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
    <div class="col-md-6 mb-2"><label class="form-label">{{ __('messages.email') }}</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
    <div class="col-md-6 mb-2"><label class="form-label">{{ __('messages.phone') }}</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.status') }}</label><select name="status" class="form-select">
        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
        <option value="2" {{ $user->status == 2 ? 'selected' : '' }}>{{ __('messages.banned') }}</option>
    </select></div>
    <div class="col-md-3 mb-2"><label class="form-label">{{ __('messages.new_password') }}</label><input type="password" name="password" class="form-control" placeholder="{{ __('messages.leave_blank') }}"></div>
</div>

<hr>
<h6 class="fw-bold mb-3"><i class="bi bi-shield-lock"></i> {{ __('messages.assign_roles') ?? 'Assign Roles' }}</h6>
<div class="row">
    @foreach($allRoles as $role)
    <div class="col-md-4 mb-2">
        <div class="form-check">
            <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" id="role_{{ $role->id }}" {{ in_array($role->id, $assignedRoleIds) ? 'checked' : '' }}>
            <label class="form-check-label" for="role_{{ $role->id }}">
                <strong>{{ $role->name }}</strong>
                @if($role->is_system)<span class="badge bg-warning text-dark ms-1">{{ __("messages.system_badge") }}</span>@endif
                <br><small class="text-muted">{{ $role->description }}</small>
            </label>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ __('messages.save') }}</button>
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
</div>
</form>
</div></div>
@endsection
