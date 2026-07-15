<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected function casts(): array
    {
        return ['balance' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getBalance(int $userId): int
    {
        $record = self::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
        return $record->balance;
    }

    public static function addPoints(int $userId, int $points, string $type, string $description, $reference = null): void
    {
        $record = self::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
        $record->increment('balance', $points);
        $record->refresh();

        PointLog::create([
            'user_id' => $userId,
            'type' => $type,
            'points' => $points,
            'description' => $description,
            'reference_id' => $reference?->id,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }

    public static function deductPoints(int $userId, int $points, string $type, string $description, $reference = null): void
    {
        $record = self::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
        $record->decrement('balance', $points);

        PointLog::create([
            'user_id' => $userId,
            'type' => $type,
            'points' => -$points,
            'description' => $description,
            'reference_id' => $reference?->id,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }
}