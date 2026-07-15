
@extends('layouts.admin')

@section('title', __('messages.user_details'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-person"></i> {{ __('messages.user_details') }}</h4>
<a href="{{ route('admin.users.index') }}" class="btn btn-link mb-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6 class="fw-bold">{{ __('messages.profile') }}</h6>
            <p class="mb-1"><strong>{{ __('messages.name') }}:</strong> {{ $user->name }}</p>
            <p class="mb-1"><strong>{{ __('messages.email') }}:</strong> {{ $user->email }}</p>
            <p class="mb-1"><strong>{{ __('messages.phone') }}:</strong> {{ $user->phone ?? '-' }}</p>
            <p class="mb-1"><strong>{{ __('messages.role') }}:</strong> <span class="badge bg-info">{{ __('messages.role_'.$user->role) }}</span></p>
            <p class="mb-1"><strong>RBAC Roles:</strong>
                @forelse($user->roles as $r)
                    <span class="badge bg-{{ $r->is_system ? 'warning text-dark' : 'secondary' }} me-1">{{ $r->name }}</span>
                @empty
                    <span class="text-muted">—</span>
                @endforelse
                <a href="{{ route('admin.users.roles.edit', $user) }}" class="btn btn-sm btn-outline-primary ms-2">
                    <i class="bi bi-pencil"></i> Edit Roles
                </a>
            </p>
            <p class="mb-1"><strong>{{ __('messages.status') }}:</strong> <span class="badge bg-{{ $user->status == 1 ? 'success' : 'secondary' }}">{{ $user->status == 1 ? __('messages.active') : __('messages.inactive') }}</span></p>
            <p class="mb-0"><strong>{{ __('messages.registered') }}:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6 class="fw-bold">{{ __('messages.addresses') }} ({{ $user->addresses->count() }})</h6>
            @forelse($user->addresses as $addr)
            <div class="mb-2 pb-2 border-bottom">
                <strong>{{ $addr->first_name }} {{ $addr->last_name }}</strong><br>
                <small class="text-muted">{{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }} {{ $addr->zipcode }}</small>
            </div>
            @empty
            <p class="text-muted">{{ __('messages.no_addresses_yet') }}</p>
            @endforelse
        </div></div>
    </div>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header bg-light fw-bold">
        <i class="bi bi-clock-history"></i> Role &amp; Permission Audit Log
        <span class="badge bg-secondary ms-2">{{ $auditLogs->count() }}</span>
    </div>
    <div class="card-body p-0">
        @if($auditLogs->isEmpty())
            <p class="text-muted p-3 mb-0">No role/permission changes recorded yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.when') }}</th>
                        <th>{{ __('messages.actor') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                        <th>{{ __('messages.subject') }}</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $log)
                    <tr>
                        <td><small>{{ $log->created_at->format('Y-m-d H:i') }}</small></td>
                        <td><small>{{ $log->actor?->name ?? 'System' }}</small></td>
                        <td>
                            @if($log->action === 'role_assigned')
                                <span class="badge bg-success">{{ __("messages.assigned") }}</span>
                            @elseif($log->action === 'role_removed')
                                <span class="badge bg-danger">{{ __("messages.removed") }}</span>
                            @elseif($log->action === 'permission_granted')
                                <span class="badge bg-info">{{ __("messages.granted") }}</span>
                            @elseif($log->action === 'permission_revoked')
                                <span class="badge bg-warning text-dark">{{ __("messages.revoked") }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td><code>{{ $log->subject_name ?? $log->subject_type }}</code></td>
                        <td><small class="text-muted">{{ $log->ip_address ?? '-' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection