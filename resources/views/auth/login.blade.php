@extends('layouts.app')

@section('title', __('messages.login'))

@push('styles')
<style>
    .auth-wrapper { min-height: calc(100vh - 80px); display: flex; align-items: center; }
    .auth-left { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 50px 40px; color: #fff; position: relative; overflow: hidden; }
    .auth-left::before { content: ''; position: absolute; top: -60px; right: -60px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.1); }
    .auth-left::after { content: ''; position: absolute; bottom: -40px; left: -40px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.08); }
    .auth-left h2 { font-weight: 800; font-size: 2rem; margin-bottom: 1rem; }
    .auth-left .feat { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
    .auth-left .feat i { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .auth-right { background: #fff; border-radius: 20px; padding: 45px 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
    .auth-right .form-control { border-radius: 12px; padding: 12px 16px; border: 2px solid #f0f0f0; transition: all .2s; }
    .auth-right .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 4px rgba(102,126,234,0.1); }
    .auth-right .btn-primary { border-radius: 12px; padding: 12px; font-weight: 700; background: linear-gradient(135deg, #667eea, #764ba2); border: none; }
    .auth-right .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102,126,234,0.4); }
    .social-btn { border: 2px solid #f0f0f0; border-radius: 12px; padding: 10px; font-weight: 600; transition: all .2s; }
    .social-btn:hover { border-color: #667eea; background: #f8f9ff; }
   .input-icon { position: relative; }
   .input-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #aaa; }
  .input-icon input { padding-left: 42px; }
  .input-icon.has-toggle input { padding-right: 48px; }
   input[type="password"]::-ms-reveal,
   input[type="password"]::-ms-clear { display: none !important; }
  .toggle-pwd { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa; background: none; border: none; z-index: 2; }
    .auth-right .input-icon input { padding-left: 42px; }
    .auth-right .input-icon.has-toggle input { padding-right: 48px; }
    input[type="password"]::-webkit-reveal-button { display: none !important; }
    .toggle-pwd { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; cursor: pointer; color: #aaa; background: none; border: none; z-index: 2; line-height: 1; }
    .toggle-pwd:hover { color: #667eea; }
</style>
@endpush

@section('content')
<div class="auth-wrapper py-5">
    <div class="container">
        <div class="row g-0 justify-content-center align-items-center" style="max-width: 920px; margin: 0 auto;">
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-left">
                    <h2>{!! __("messages.welcome_back_title") !!} ??</h2>
                    <p class="opacity-75 mb-4">{{ __("messages.login_subtitle") }}</p>
                    <div class="feat">
                        <i class="bi bi-truck"></i>
                        <div><div class="fw-semibold">{{ __("messages.free_worldwide_shipping") }}</div><div class="small opacity-75">{{ __("messages.on_orders_over") }}</div></div>
                    </div>
                    <div class="feat">
                        <i class="bi bi-shield-check"></i>
                        <div><div class="fw-semibold">{{ __("messages.secure_trusted") }}</div><div class="small opacity-75">{{ __("messages.stripe_protected") }}</div></div>
                    </div>
                    <div class="feat">
                        <i class="bi bi-lightning-charge"></i>
                        <div><div class="fw-semibold">{{ __("messages.flash_sales_deals") }}</div><div class="small opacity-75">{{ __("messages.up_to_off") }}</div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="auth-right">
                    <div class="text-center mb-4">
                        <i class="bi bi-globe2 text-primary" style="font-size:2.2rem;"></i>
                        <h4 class="fw-bold mt-2 mb-0">{{ __("messages.sign_in") }}</h4>
                        <small class="text-muted">{{ __("messages.enter_credentials") }}</small>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __("messages.email_address") }}</label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" class="form-control" placeholder="{{ __('messages.enter_email') }}" required>
                            </div>
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ __("messages.password") }}</label>
                            <div class="input-icon has-toggle">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="{{ __('messages.enter_password') }}" required>
                                <button type="button" class="toggle-pwd" onclick="togglePwd()"><i class="bi bi-eye" id="pwdIcon"></i></button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">{{ __("messages.remember_me") }}</label>
                            </div>
                            <a href="#" class="small text-decoration-none">{{ __("messages.forgot_password") }}</a>
                        </div>
                        @if(config("services.recaptcha.enabled", false))
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ config("services.recaptcha.site_key") }}"></div>
                            @error("g-recaptcha-response")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        @endif
                        <button type="submit" class="btn btn-primary w-100 mb-3">{{ __("messages.sign_in") }} <i class="bi bi-arrow-right ms-1"></i></button>
                    </form>

                    <div class="text-center text-muted small my-3">{{ __("messages.or_continue_with") }}</div>
                    <div class="row g-2">
                        <div class="col-4"><button class="social-btn w-100"><i class="bi bi-google text-danger"></i></button></div>
                        <div class="col-4"><button class="social-btn w-100"><i class="bi bi-facebook text-primary"></i></button></div>
                        <div class="col-4"><button class="social-btn w-100"><i class="bi bi-apple"></i></button></div>
                    </div>

                    <p class="text-center mt-4 mb-0 text-muted small">
                        {{ __('messages.dont_have_account') }} <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">{{ __("messages.sign_up_free") }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('password');
    const icon = document.getElementById('pwdIcon');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
    else { input.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
@endsection
