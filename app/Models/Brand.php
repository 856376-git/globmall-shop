<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Brand extends Model
{
    protected $fillable = [
        'slug', 'logo', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function translations()
    {
        return $this->hasMany(BrandTranslation::class);
    }

    public function translation(string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        return $this->hasOne(BrandTranslation::class)->where('locale', $locale);
    }

    public function getNameAttribute()
    {
        return $this->translation?->name;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
