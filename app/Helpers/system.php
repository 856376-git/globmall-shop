<?php

use App\Models\SystemConfig;

if (!function_exists('sys_config')) {
    function sys_config(string $key, mixed $default = null): mixed
    {
        return SystemConfig::getVal($key, $default);
    }
}

if (!function_exists('sys_config_group')) {
    function sys_config_group(string $group): array
    {
        return SystemConfig::getGroup($group);
    }
}
