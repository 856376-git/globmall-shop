<?php
// 6. 创建 SendSmsVerify Service
$file = <<<'PHPCODE'
<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SendSmsVerify
{
    protected Client $twilio;
    protected string $verifyCodeDuration = 'minutes:5';
    protected string $verificationCodeLength = 6;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }

    public function sendVerification(string $phone): string
    {
        $phone = $this->formatPhone($phone);
        
        $code = $this->generateCode();
        $token = Str::random(40); // 唯一标识，用于拒绝重复使用
        
        Cache::put("sms_verify:{$phone}:code", $code, config('services.twilio.verify_duration', 300));
        Cache::put("sms_verify:{$phone}:token", $token, config('services.twilio.verify_duration', 300));
        
        $message = "Your GlobMall verification code is {$code}. Valid for 5 minutes.";
        
        try {
            $this->twilio->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $message
                ]
            );
            return $token;
        } catch (\Exception $e) {
            \Log::error('SMS verification failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to send verification code. Please try again.');
        }
    }

    public function verifyCode(string $phone, string $code): bool
    {
        $phone = $this->formatPhone($phone);
        $cachedCode = Cache::get("sms_verify:{$phone}:code");
        $cachedToken = Cache::get("sms_verify:{$phone}:token");
        
        if (!$cachedCode || !$cachedToken) {
            throw new \RuntimeException('Verification code expired or invalid.');
        }
        
        // 验证码匹配
        if ($cachedCode !== $code) {
            throw new \RuntimeException('Incorrect verification code.');
        }
        
        // 拒绝重复使用
        if (!empty($sessionToken) && $sessionToken !== $cachedToken) {
            throw new \RuntimeException('Verification code already used or invalid.');
        }
        
        // 标记为已使用
        Cache::forget("sms_verify:{$phone}:code");
        Cache::put("sms_verify:{$phone}:verified", true, config('services.twilio.verify_duration', 300));
        
        return true;
    }

    public function resetVerification(string $phone): void
    {
        $phone = $this->formatPhone($phone);
        Cache::forget("sms_verify:{$phone}:*");
    }

    protected function formatPhone(string $phone): string
    {
        // 移除所有非数字字符
        return preg_replace('/[^0-9]/', '', $phone);
    }

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), $this->verificationCodeLength, '0', STR_PAD_LEFT);
    }
}
PHPCODE;

file_put_contents('SendSmsVerify.php', $file, LOCK_EX);
echo "SendSmsVerify.php created OK\n";
