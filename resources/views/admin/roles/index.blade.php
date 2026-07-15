
@extends('layouts.admin')

@section('title', __('messages.role_management'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-shield-lock"></i> {{ __('messages.role_management') }}</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">{{ __('messages.roles_description') ?? 'Manage roles and their permissions.' }}</p>
    <div>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-info btn-sm me-2"><i class="bi bi-key"></i> {{ __('messages.view_permissions') ?? 'View Permissions' }}</a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> {{ __('messages.new_role') ?? 'New Role' }}</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.slug') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.permissions') ?? 'Permissions' }}</th>
                    <th>{{ __('messages.users') ?? 'Users' }}</th>
                    <th>{{ __('messages.sort_order') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td><strong>{{ $role->name }}</strong></td>
                    <td><code>{{ $role->slug }}</code></td>
                    <td><small class="text-muted">{{ $role->description }}</small></td>
                    <td><span class="badge bg-info">{{ $role->permissions_count }}</span></td>
                    <td><span class="badge bg-secondary">{{ $role->users_count }}</span></td>
                    <td>{{ $role->sort_order }}</td>
                    <td>
                        @if($role->is_system)
                            <span class="badge bg-warning text-dark">{{ __('messages.system') ?? 'System' }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('messages.custom') ?? 'Custom' }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        @if(!$role->is_system)
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
