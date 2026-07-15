
@extends('layouts.admin')

@section('title', __('messages.manage_points') . ' - ' . $user->name)

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-coin"></i> {{ __('messages.manage_points') }} - {{ $user->name }}</h4>
<a href="{{ route('admin.members.index') }}" class="btn btn-link mb-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3"><div class="card-body text-center">
            <h6 class="text-muted">{{ __('messages.points_balance') }}</h6>
            <h2 class="fw-bold">{{ number_format(\App\Models\UserPoints::getBalance($user->id)) }}</h2>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6 class="fw-bold mb-3">{{ __('messages.add_points') }}</h6>
            <form action="{{ route('admin.members.points.add', $user->id) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-auto"><input type="number" name="amount" class="form-control" placeholder="0" required></div>
                <div class="col-auto"><input type="text" name="reason" class="form-control" placeholder="Reason"></div>
                <div class="col-auto"><button class="btn btn-success"><i class="bi bi-plus-lg"></i> {{ __('messages.add') }}</button></div>
            </form>
        </div></div>
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="fw-bold mb-3">{{ __('messages.deduct_points') }}</h6>
            <form action="{{ route('admin.members.points.deduct', $user->id) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-auto"><input type="number" name="amount" class="form-control" placeholder="0" required></div>
                <div class="col-auto"><input type="text" name="reason" class="form-control" placeholder="Reason"></div>
                <div class="col-auto"><button class="btn btn-warning"><i class="bi bi-dash-lg"></i> {{ __('messages.deduct') }}</button></div>
            </form>
        </div></div>
    </div>
</div>

<div class="card shadow-sm mt-3"><div class="card-body">
    <h6 class="fw-bold mb-3">{{ __('messages.point_history') }}</h6>
    <table class="table table-sm">
        <thead><tr><th>{{ __('messages.date') }}</th><th>Type</th><th>{{ __('messages.amount') }}</th><th>Reason</th></tr></thead>
        <tbody>
            @foreach($logs ?? [] as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                <td><span class="badge bg-{{ $log->type == 'add' ? 'success' : 'warning' }}">{{ ucfirst($log->type) }}</span></td>
                <td>{{ $log->type == 'add' ? '+' : '-' }}{{ number_format($log->amount) }}</td>
                <td>{{ $log->reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div></div>
@endsection
