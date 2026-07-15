<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'locale', 'name', 'description', 'short_description',
        'meta_title', 'meta_description',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}