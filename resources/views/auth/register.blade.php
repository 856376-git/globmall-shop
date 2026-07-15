@extends('layouts.app')

@section('title', __('messages.register'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-4">
                <h4 class="fw-bold text-center mb-4"><i class="bi bi-person-plus"></i> {{ __('messages.register') }}</h4>

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __("messages.name") }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __("messages.email") }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __("messages.password") }}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __("messages.confirm_password") }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    @if(config("services.recaptcha.enabled", false))
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config("services.recaptcha.site_key") }}"></div>
                        @error("g-recaptcha-response")
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.register') }}</button>
                </form>

                <p class="text-center mt-3 text-muted">
                    {{ __("messages.already_have_account") }} <a href="{{ route('login') }}">{{ __('messages.login') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection