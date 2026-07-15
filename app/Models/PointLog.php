<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    protected $fillable = [
        'user_id', 'type', 'points', 'description', 'reference_id', 'reference_type',
    ];

    protected function casts(): array
    {
        return ['points' => 'integer', 'reference_id' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}