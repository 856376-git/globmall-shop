
@extends('layouts.admin')

@section('title', __('messages.permissions') ?? 'Permissions')

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-key"></i> {{ __('messages.all_permissions') ?? 'All Permissions' }}</h4>

<div class="row">
    @foreach($permissions as $group => $perms)
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0">{{ ucfirst($group) }} <span class="badge bg-secondary">{{ count($perms) }}</span></h6>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($perms as $perm)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $perm->name }}</strong>
                        <br><code class="small text-muted">{{ $perm->slug }}</code>
                    </div>
                    <span class="badge bg-info rounded-pill">
                        {{ $roles->filter(fn($r) => $r->permissions->contains($perm->id))->count() }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endforeach
</div>
@endsection
