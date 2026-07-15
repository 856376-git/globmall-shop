
@extends('layouts.admin')

@section('title', __('messages.products'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> {{ __('messages.products') }}</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> {{ __('messages.new_product') }}</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>{{ __('messages.image') }}</th><th>{{ __('messages.sku') }}</th><th>{{ __('messages.name') }}</th><th>{{ __('messages.category') }}</th><th>{{ __('messages.price') }}</th><th>{{ __('messages.stock') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.actions') }}</th></tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        @if($product->primaryImage)@php $adminImg = $product->primaryImage?->image; $adminIsExt = $adminImg && str_starts_with($adminImg, "http"); @endphp<img src="{{ $adminIsExt ? $adminImg : asset($adminImg) }}" style="width:40px;height:40px;object-fit:cover;" class="rounded">
                        @else<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-image"></i></div>@endif
                    </td>
                    <td><code>{{ $product->sku }}</code></td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>
                        <span class="{{ $product->isLowStock() ? 'text-danger fw-bold' : '' }}">{{ $product->stock }}</span>
                    </td>
                    <td><span class="badge bg-{{ $product->status ? 'success' : 'secondary' }}">{{ $product->status ? __('messages.active') : __('messages.inactive') }}</span></td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $products->links() }}
@endsection
