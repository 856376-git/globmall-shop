
@extends('layouts.admin')

@section('title', __('messages.brands'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-award"></i> {{ __('messages.brands') }}</h4>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> {{ __('messages.new_brand') }}</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>{{ __('messages.name_en') }}</th><th>{{ __('messages.name_zh') }}</th><th>{{ __('messages.slug') }}</th><th>{{ __('messages.actions') }}</th></tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td>{{ $brand->translations->where('locale','en')->first()?->name }}</td>
                    <td>{{ $brand->translations->where('locale','zh')->first()?->name }}</td>
                    <td><code>{{ $brand->slug }}</code></td>
                    <td>
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
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
{{ $brands->links() }}
@endsection
