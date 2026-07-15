<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'john@test.com')->first();
echo "User: {$user->email}\n";
echo "Password hash: " . substr($user->password, 0, 20) . "...\n";
echo "Password check: " . (Illuminate\Support\Facades\Hash::check('123456', $user->password) ? 'OK' : 'FAIL') . "\n";
echo "Status: {$user->status}\n";
echo "Role: {$user->role}\n";
