<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'brand_id', 'locale', 'name', 'description',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
