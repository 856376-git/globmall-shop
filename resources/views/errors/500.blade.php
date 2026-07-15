<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>500 - {{ __('messages.server_error') }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { font-family: system-ui, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.error-code { font-size: 8rem; font-weight: 800; color: #ef4444; line-height: 1; }
</style>
</head>
<body>
<div class="text-center">
    <div class="error-code">500</div>
    <h2 class="mt-3 mb-2">{{ __('messages.server_error') }}</h2>
    <p class="text-muted mb-4">{{ __('messages.500_desc') }}</p>
    <a href="{{ route("home") }}" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> {{ __('messages.back_to_home') }}</a>
</div>
</body>
</html>