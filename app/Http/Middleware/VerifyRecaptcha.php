<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next): Response
    {
        // 如果未启用 reCAPTCHA，直接放行
        if (!config('services.recaptcha.enabled', false)) {
            return $next($request);
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        if (!$recaptchaResponse) {
            return back()->with('error', 'Please complete the reCAPTCHA verification.')->withInput();
        }
        
        $secret = config('services.recaptcha.secret');
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptchaResponse}&remoteip=" . $request->ip());
        $captchaSuccess = json_decode($verify);
        
        if (!$captchaSuccess->success) {
            return back()->with('error', 'reCAPTCHA verification failed. Please try again.')->withInput();
        }
        
        return $next($request);
    }
}