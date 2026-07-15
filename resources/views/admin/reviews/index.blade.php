
@extends('layouts.admin')

@section('title', __('messages.review_management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-chat-square-text"></i> {{ __('messages.review_management') }}</h4>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('messages.approved') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm">{{ __('messages.filter') }}</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>{{ __('messages.product') }}</th><th>{{ __('messages.user') }}</th><th>{{ __('messages.rating') }}</th><th>{{ __('messages.title') }}</th><th>{{ __('messages.content') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.date') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td>{{ $review->product->name }}</td>
                    <td>{{ $review->user->name }}</td>
                    <td><span class="text-warning">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span></td>
                    <td>{{ $review->title }}</td>
                    <td>{{ Str::limit($review->content, 50) }}</td>
                    <td><span class="badge bg-{{ $review->is_approved ? 'success' : 'warning' }}">{{ $review->is_approved ? __('messages.approved') : __('messages.pending') }}</span></td>
                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                    <td>
                        @if(!$review->is_approved)<form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">@csrf<button class="btn btn-success btn-sm">{{ __('messages.approve') }}</button></form>@endif
                        @if($review->is_approved)<form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">@csrf<button class="btn btn-warning btn-sm">{{ __('messages.reject') }}</button></form>@endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $reviews->links() }}
@endsection
