<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group_name', 'label', 'options', 'sort_order', 'status'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function getVal(string $key, mixed $default = null): mixed
    {
        return Cache::remember("sys_config:{$key}", 3600, function () use ($key, $default) {
            $config = static::where('key', $key)->where('status', true)->first();
            if (!$config) return $default;
            return match ($config->type) {
                'integer' => (int) $config->value,
                'float' => (float) $config->value,
                'boolean' => boolval($config->value),
                'json' => json_decode($config->value, true),
                default => $config->value,
            };
        });
    }

    public static function setVal(string $key, mixed $value, string $type = 'string', ?string $label = null, ?string $group = null): void
    {
        $config = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
                'label' => $label ?? $key,
                'group_name' => $group ?? 'general',
            ]
        );
        Cache::forget("sys_config:{$key}");
    }

    public static function getGroup(string $group): array
    {
        return static::where('group_name', $group)
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("sys_config:{$key}");
        }
    }
}
