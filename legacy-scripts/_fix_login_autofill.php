<?php
$file = file_get_contents('resources/views/auth/login.blade.php');

// 移除 value="{{ old('email') }}"
$file = str_replace('value="{{ old(\'email\') }}"', '', $file);

// 添加 autocomplete="off" 到表单
$file = str_replace('<form action="{{ route(\'login\') }}" method="POST">', '<form action="{{ route(\'login\') }}" method="POST" autocomplete="off">', $file);

file_put_contents('resources/views/auth/login.blade.php', $file, LOCK_EX);
echo "Login form fixed!\n";
