
@extends('layouts.admin')

@section('title', __('messages.members'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> {{ __('messages.members') }}</h2>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">{{ __('messages.search') }}</button>
    </div>
</form>

<table class="table table-bordered bg-white">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.email') }}</th>
            <th>{{ __('messages.role') }}</th>
            <th>{{ __('messages.points') }}</th>
            <th>{{ __('messages.registered') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
        @php $pts = \App\Models\UserPoints::getBalance($u->id); @endphp
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
            <td><span class="badge bg-warning text-dark">{{ number_format($pts) }}</span></td>
            <td>{{ $u->created_at->format('Y-m-d') }}</td>
            <td><a href="{{ route('admin.members.points', $u->id) }}" class="btn btn-sm btn-primary">{{ __('messages.manage_points') }}</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">{{ __('messages.no_members_found') }}</td></tr>
        @endforelse
    </tbody>
</table>
{{ $users->withQueryString()->links() }}
@endsection
