
@extends('layouts.admin')

@section('title', __('messages.system_config'))

@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-gear"></i> {{ __('messages.system_config') }}</h4>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6 class="fw-bold mb-3">{{ __('messages.add_new_config') ?? 'Add New Config' }}</h6>
    <form action="{{ route('admin.system-config.store') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-2"><input type="text" name="key" class="form-control" placeholder="{{ __('messages.key') ?? 'Key' }}" required></div>
        <div class="col-md-3"><input type="text" name="value" class="form-control" placeholder="{{ __('messages.value') ?? 'Value' }}" required></div>
        <div class="col-md-2"><select name="type" class="form-select"><option value="string">String</option><option value="integer">Integer</option><option value="boolean">Boolean</option><option value="json">JSON</option></select></div>
        <div class="col-md-3"><input type="text" name="description" class="form-control" placeholder="Description"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('messages.save') }}</button></div>
    </form>
</div></div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>{{ __('messages.key') ?? 'Key' }}</th><th>{{ __('messages.value') ?? 'Value' }}</th><th>Type</th><th>Description</th><th>{{ __('messages.actions') }}</th></tr></thead>
            <tbody>
                @foreach($configs ?? [] as $config)
                <tr>
                    <td><code>{{ $config->key }}</code></td>
                    <td>{{ Str::limit($config->value, 50) }}</td>
                    <td><span class="badge bg-info">{{ $config->type }}</span></td>
                    <td>{{ $config->description ?? '-' }}</td>
                    <td>
                        <form action="{{ route('admin.system-config.destroy', $config) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
