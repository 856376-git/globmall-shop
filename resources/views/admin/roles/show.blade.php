@extends('layouts.admin')

@section('title', $role->name)

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-shield-lock"></i> {{ $role->name }}
    @if($role->is_system)
        <span class="badge bg-warning text-dark ms-2">{{ __('messages.system') ?? 'System' }}</span>
    @endif
</h4>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">{{ __('messages.basic_info') ?? 'Basic Info' }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">{{ __('messages.name') }}</span>
                    <strong>{{ $role->name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">{{ __('messages.slug') }}</span>
                    <code>{{ $role->slug }}</code>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">{{ __('messages.sort_order') }}</span>
                    <span>{{ $role->sort_order }}</span>
                </li>
                <li class="list-group-item">
                    <div class="text-muted mb-1">{{ __('messages.description') }}</div>
                    <div>{{ $role->description ?: '—' }}</div>
                </li>
            </ul>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-key"></i> {{ __('messages.permissions') ?? 'Permissions' }}</span>
                <span class="badge bg-info">{{ $role->permissions->count() }}</span>
            </div>
            <div class="card-body">
                @php
                    $grouped = $role->permissions->groupBy('group');
                @endphp
                @forelse($grouped as $group => $perms)
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary">{{ ucfirst($group) }}</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($perms as $perm)
                                <span class="badge bg-light text-dark border" title="{{ $perm->slug }}">
                                    <i class="bi bi-check2 text-success"></i> {{ $perm->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('messages.no_permissions') ?? 'No permissions assigned.' }}</p>
                @endforelse
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-people"></i> {{ __('messages.assigned_users') ?? 'Assigned Users' }}
                <span class="badge bg-secondary">{{ $role->users->count() }}</span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($role->users as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $user->name }}</strong>
                            <small class="text-muted ms-2">{{ $user->email }}</small>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $user->role }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('messages.no_users') ?? 'No users assigned.' }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
