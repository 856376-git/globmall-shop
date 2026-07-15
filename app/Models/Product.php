<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand_id', 'sku', 'slug', 'price', 'compare_price',
        'cost', 'stock', 'low_stock_threshold', 'weight',
        'is_featured', 'is_new', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        return $this->hasOne(ProductTranslation::class)->where('locale', $locale);
    }

    public function getNameAttribute()
    {
        return $this->translation?->name;
    }

    public function getDescriptionAttribute()
    {
        return $this->translation?->description;
    }

    public function getShortDescriptionAttribute()
    {
        return $this->translation?->short_description;
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->approved()->latest();
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', 1);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function getDiscountPercentAttribute(): ?float
    {
        if ($this->compare_price && $this->compare_price > 0) {
            return round(($this->compare_price - $this->price) / $this->compare_price * 100, 0);
        }
        return null;
    }

    public function getAverageRatingAttribute()
    {
        return round($this->allReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute()
    {
        return $this->allReviews()->count();
    }
}