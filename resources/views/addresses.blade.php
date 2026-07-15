@extends('layouts.app')

@section('title', __('messages.my_addresses'))

@section('content')
<h3 class="mb-4"><i class="bi bi-geo-alt"></i> {{ __('messages.my_addresses') }}</h3>

<div class="row">
    <!-- 地址列表 -->
    <div class="col-md-7">
        @if($addresses->count())
            @foreach($addresses as $addr)
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        @if($addr->is_default)<span class="badge bg-primary mb-2">{{ __('messages.default') }}</span>@endif
                        @if($addr->label)<span class="badge bg-secondary mb-2">{{ $addr->label }}</span>@endif
                        <h6 class="fw-bold">{{ $addr->first_name }} {{ $addr->last_name }}</h6>
                        <p class="mb-1">{{ $addr->address_line1 }}</p>
                        @if($addr->address_line2)<p class="mb-1">{{ $addr->address_line2 }}</p>@endif
                        <p class="mb-1">{{ $addr->city }}, {{ $addr->state }} {{ $addr->zipcode }}</p>
                        <p class="mb-0 text-muted">{{ $addr->country }} @if($addr->phone)| {{ $addr->phone }}@endif</p>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @if(!$addr->is_default)
                        <form action="{{ route('addresses.default', $addr->id) }}" method="POST">
                            @csrf <button class="btn btn-outline-primary btn-sm">{{ __('messages.set_default') }}</button>
                        </form>
                        @endif
                        <form action="{{ route('addresses.delete', $addr->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete_address') }}')">{{ __('messages.delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-4"><p class="text-muted">{{ __('messages.no_addresses_yet') }}</p></div>
        @endif
    </div>

    <!-- 新增地址表单 -->
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('messages.add_new_address') }}</h5>
                <form action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">{{ __('messages.label_home_office') }}</label>
                        <input type="text" name="label" class="form-control form-control-sm" placeholder="{{ __('messages.ph_label') }}">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><input type="text" name="first_name" class="form-control form-control-sm" placeholder="{{ __('messages.ph_first_name') }}" required></div>
                        <div class="col-6"><input type="text" name="last_name" class="form-control form-control-sm" placeholder="{{ __('messages.ph_last_name') }}" required></div>
                    </div>
                    <div class="mb-2"><input type="text" name="phone" class="form-control form-control-sm" placeholder="{{ __('messages.ph_phone') }}"></div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <select name="country" class="form-select form-select-sm" required>
                                <option value="US">United States</option>
                                <option value="GB">United Kingdom</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                                <option value="CN">China</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="JP">Japan</option>
                            </select>
                        </div>
                        <div class="col-6"><input type="text" name="state" class="form-control form-control-sm" placeholder="{{ __('messages.ph_state_province') }}" required></div>
                    </div>
                    <div class="mb-2"><input type="text" name="city" class="form-control form-control-sm" placeholder="{{ __('messages.ph_city') }}" required></div>
                    <div class="mb-2"><input type="text" name="address_line1" class="form-control form-control-sm" placeholder="{{ __('messages.ph_address_line1') }}" required></div>
                    <div class="mb-2"><input type="text" name="address_line2" class="form-control form-control-sm" placeholder="{{ __('messages.ph_address_line2') }}"></div>
                    <div class="mb-2"><input type="text" name="zipcode" class="form-control form-control-sm" placeholder="{{ __('messages.ph_zipcode') }}" required></div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_default" class="form-check-input" id="isDefault" value="1">
                        <label class="form-check-label" for="isDefault">{{ __('messages.set_as_default_address') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection