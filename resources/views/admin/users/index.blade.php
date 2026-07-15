
@extends('layouts.admin')

@section('title', __('messages.users'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-people"></i> {{ __('messages.users') }}</h4>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_roles') }}</option>
                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>{{ __('messages.customer') }}</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('messages.role_admin') }}</option>
                    <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>{{ __('messages.seller') }}</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm">{{ __('messages.filter') }}</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>{{ __('messages.name') }}</th><th>{{ __('messages.email') }}</th><th>{{ __('messages.roles') ?? 'Roles' }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.registered') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->roles->count())
                            @foreach($user->roles as $role)
                                <span class="badge bg-{{ $role->slug == 'super-admin' ? 'danger' : ($role->slug == 'admin' ? 'warning' : 'info') }}">{{ $role->name }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">{{ __('messages.customer') }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $user->status ? 'success' : 'secondary' }}">{{ $user->status ? __('messages.active') : __('messages.inactive') }}</span></td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.view') }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $users->links() }}
@endsection
