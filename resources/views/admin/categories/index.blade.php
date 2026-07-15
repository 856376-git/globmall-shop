
@extends('layouts.admin')

@section('title', __('messages.category_management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-tags"></i> {{ __('messages.category_management') }}</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> {{ __('messages.new_category') }}</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>{{ __('messages.slug') }}</th><th>{{ __('messages.name_en') }}</th><th>{{ __('messages.name_zh') }}</th><th>{{ __('messages.parent') }}</th><th>{{ __('messages.sort_order') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.actions') }}</th></tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td>{{ $cat->translations->where('locale','en')->first()?->name }}</td>
                    <td>{{ $cat->translations->where('locale','zh')->first()?->name }}</td>
                    <td>{{ $cat->parent_id ? $cat->parent?->name : '-' }}</td>
                    <td>{{ $cat->sort_order }}</td>
                    <td><span class="badge bg-{{ $cat->status ? 'success' : 'secondary' }}">{{ $cat->status ? __('messages.active') : __('messages.inactive') }}</span></td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $categories->links() }}
@endsection
