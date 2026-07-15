<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'slug', 'image', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        return $this->hasOne(CategoryTranslation::class)->where('locale', $locale);
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

    public function scopeTopLevel($query)
    {
        return $query->where('parent_id', 0);
    }
}